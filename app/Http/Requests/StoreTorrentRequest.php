<?php

declare(strict_types=1);

/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D Community Edition is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D Community Edition
 *
 * @author     Roardom <roardom@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Http\Requests;

use App\Attributes\FromCurrentUserId;
use App\Enums\ModerationStatus;
use App\Helpers\Bencode;
use App\Helpers\TorrentTools;
use App\Models\Category;
use App\Models\Distributor;
use App\Models\Region;
use App\Models\Resolution;
use App\Models\Scopes\ApprovedScope;
use App\Models\Torrent;
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule as LaravelRule;
use Closure;
use Exception;
use Override;
use Spatie\LaravelData\Attributes\Hidden;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\Between;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\File;
use Spatie\LaravelData\Attributes\Validation\Image;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

#[MergeValidationRules]
class StoreTorrentRequest extends Data
{
    public function __construct(
        #[File, Hidden, Max(2 * 1024 * 1024)]
        public UploadedFile $torrent,
        #[Unique(Torrent::class, withoutTrashed: true), Max(255)]
        public string $name,
        #[Max(65535)]
        public string $description,
        #[FromCurrentUserId]
        public int $userId,
        #[Exists(Category::class, 'id')]
        public int $categoryId,
        #[Exists(Type::class, 'id')]
        public int $typeId,
        #[File, Max(2 * 1024 * 1024)]
        public ?UploadedFile $nfo = null,
        #[MapInputName('torrent-cover'), File, Image, Max(2 * 1024 * 1024), Hidden]
        public ?UploadedFile $poster = null,
        #[MapInputName('torrent-banner'), File, Image, Max(2 * 1024 * 1024), Hidden]
        public ?UploadedFile $backdrop = null,
        #[Max(2 * 1024 * 1024)]
        public ?string $mediainfo = null,
        #[Max(2 * 1024 * 1024)]
        public ?string $bdinfo = null,
        #[Exists(Resolution::class, 'id')]
        public ?int $resolutionId = null,
        #[Exists(Region::class, 'id')]
        public ?int $regionId = null,
        #[Exists(Distributor::class, 'id')]
        public ?int $distributorId = null,
        #[Min(0)]
        public ?int $imdb = null,
        #[Min(0)]
        public ?int $tvdb = null,
        #[Min(0)]
        public ?int $tmdbMovieId = null,
        #[Min(0)]
        public ?int $tmdbTvId = null,
        #[Min(0)]
        public ?int $mal = null,
        #[Min(0)]
        public ?int $igdb = null,
        #[Min(0)]
        public ?int $seasonNumber = null,
        #[Min(0)]
        public ?int $episodeNumber = null,
        public bool $anon = false,
        public bool $personalRelease = false,
        public bool $internal = false,
        #[Between(0, 100)]
        public int $free = 0,
        public bool $refundable = false,
        #[Hidden]
        public ?string $keywords = null,
        #[Hidden]
        public bool $modQueueOptIn = false,
    ) {
    }

    /**
     * @param array<mixed> $properties
     *
     * @return array<mixed>
     */
    #[Override]
    public static function prepareForPipeline(array $properties): array
    {
        $properties['tmdb_movie_id'] = \array_key_exists('movie_exists_on_tmdb', $properties) ? ($properties['tmdb_movie_id'] ?: null) : null;
        $properties['tmdb_tv_id'] = \array_key_exists('tv_exists_on_tmdb', $properties) ? ($properties['tmdb_tv_id'] ?: null) : null;
        $properties['imdb'] = \array_key_exists('title_exists_on_imdb', $properties) ? ($properties['imdb'] ?: null) : null;
        $properties['tvdb'] = \array_key_exists('tv_exists_on_tvdb', $properties) ? ($properties['tvdb'] ?: null) : null;
        $properties['mal'] = \array_key_exists('anime_exists_on_mal', $properties) ? ($properties['mal'] ?: null) : null;
        $properties['igdb'] = \array_key_exists('game_exists_on_igdb', $properties) ? ($properties['igdb'] ?: null) : null;

        return $properties;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<Closure(string, mixed, Closure(string): never): void|\Illuminate\Validation\Rules\ProhibitedIf|\Illuminate\Validation\Rules\RequiredIf|\Illuminate\Validation\Rules\ExcludeIf|\Illuminate\Validation\ConditionalRules|\Illuminate\Validation\Rules\Unique|string>>
     */
    public static function rules(Request $request): array
    {
        $user = $request->user()->loadExists('internals');
        $category = Category::query()->findOrFail($request->integer('category_id'));

        $mustBeNull = function (string $attribute, mixed $value, callable $fail): void {
            if ($value !== null) {
                $fail("The {$attribute} must be null.");
            }
        };

        return [
            'torrent' => [
                function (string $_attribute, mixed $value, Closure $fail): void {
                    if ($value->getClientOriginalExtension() !== 'torrent') {
                        $fail('The torrent file uploaded does not have a ".torrent" file extension (it has "'.$value->getClientOriginalExtension().'"). Did you upload the correct file?');
                    }

                    $decodedTorrent = TorrentTools::normalizeTorrent($value);

                    $v2 = Bencode::is_v2_or_hybrid($decodedTorrent);

                    if ($v2) {
                        $fail('BitTorrent v2 (BEP 52) is not supported!');
                    }

                    try {
                        $_meta = Bencode::get_meta($decodedTorrent);
                    } catch (Exception) {
                        $fail('You Must Provide A Valid Torrent File For Upload!');
                    }

                    foreach (TorrentTools::getFilenameArray($decodedTorrent) as $name) {
                        if (!TorrentTools::isValidFilename($name)) {
                            $fail('Invalid Filenames In Torrent Files!');
                        }
                    }

                    $torrent = Torrent::query()->withoutGlobalScope(ApprovedScope::class)->where('info_hash', '=', Bencode::get_infohash($decodedTorrent))->first();

                    if ($torrent !== null) {
                        match ($torrent->status) {
                            ModerationStatus::PENDING   => $fail('A torrent with the same info_hash has already been uploaded and is pending moderation.'),
                            ModerationStatus::APPROVED  => $fail('A torrent with the same info_hash has already been uploaded and has been approved.'),
                            ModerationStatus::REJECTED  => $fail('A torrent with the same info_hash has already been uploaded and has been rejected.'),
                            ModerationStatus::POSTPONED => $fail('A torrent with the same info_hash has already been uploaded and is currently postponed.'),
                        };
                    }
                }
            ],
            'nfo' => [
                function (string $_attribute, mixed $value, Closure $fail): void {
                    if ($value->getClientOriginalExtension() !== 'nfo') {
                        $fail('The NFO uploaded does not have a ".nfo" file extension (it has "'.$value->getClientOriginalExtension().'"). Did you upload the correct file?');
                    }
                },
            ],
            'resolution_id' => [
                LaravelRule::when($category->movie_meta || $category->tv_meta, [
                    'required'
                ]),
                LaravelRule::when(!$category->movie_meta && !$category->tv_meta, [
                    'nullable',
                ]),
            ],
            'imdb' => [
                LaravelRule::when($category->movie_meta || $category->tv_meta, [
                    'required_with:title_exists_on_imdb',
                ]),
                LaravelRule::when(!($category->movie_meta || $category->tv_meta), [
                    $mustBeNull,
                ]),
            ],
            'tvdb' => [
                LaravelRule::when($category->tv_meta, [
                    'required_with:tv_exists_on_tvdb',
                ]),
                LaravelRule::when(!$category->tv_meta, [
                    $mustBeNull,
                ]),
            ],
            'tmdb_movie_id' => [
                LaravelRule::when($category->movie_meta, [
                    'required_with:movie_exists_on_tmdb',
                ]),
                LaravelRule::when(!$category->movie_meta, [
                    $mustBeNull,
                ]),
            ],
            'tmdb_tv_id' => [
                LaravelRule::when($category->tv_meta, [
                    'required_with:tv_exists_on_tmdb',
                ]),
                LaravelRule::when(!$category->tv_meta, [
                    $mustBeNull,
                ]),
            ],
            'mal' => [
                LaravelRule::when($category->movie_meta || $category->tv_meta, [
                    'required_with:anime_exists_on_mal',
                ]),
                LaravelRule::when(!($category->movie_meta || $category->tv_meta), [
                    $mustBeNull,
                ]),
            ],
            'igdb' => [
                LaravelRule::when($category->game_meta, [
                    'required_with:game_exists_on_igdb',
                ]),
                LaravelRule::when(!$category->game_meta, [
                    $mustBeNull,
                ]),
            ],
            'season_number' => [
                LaravelRule::when($category->tv_meta, [
                    'required',
                ]),
                LaravelRule::prohibitedIf(!$category->tv_meta),
            ],
            'episode_number' => [
                LaravelRule::when($category->tv_meta, [
                    'required',
                ]),
                LaravelRule::prohibitedIf(!$category->tv_meta),
            ],
            'internal' => [
                /** @phpstan-ignore property.notFound (Larastan doesn't yet support loadExists()) */
                LaravelRule::requiredIf($user->group->is_modo || $user->internals_exists),
                /** @phpstan-ignore property.notFound (Larastan doesn't yet support loadExists()) */
                LaravelRule::excludeIf(!($user->group->is_modo || $user->internals_exists)),
            ],
            'free' => [
                /** @phpstan-ignore property.notFound (Larastan doesn't yet support loadExists()) */
                LaravelRule::requiredIf($user->group->is_modo || $user->internals_exists),
                /** @phpstan-ignore property.notFound (Larastan doesn't yet support loadExists()) */
                LaravelRule::excludeIf(!($user->group->is_modo || $user->internals_exists)),
            ],
            'refundable' => [
                /** @phpstan-ignore property.notFound (Larastan doesn't yet support loadExists()) */
                LaravelRule::requiredIf($user->group->is_modo || $user->internals_exists),
                /** @phpstan-ignore property.notFound (Larastan doesn't yet support loadExists()) */
                LaravelRule::excludeIf(!($user->group->is_modo || $user->internals_exists)),
            ],
        ];
    }
}
