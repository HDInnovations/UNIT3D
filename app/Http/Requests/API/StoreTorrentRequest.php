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

namespace App\Http\Requests\API;

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
use Illuminate\Support\Carbon;
use Closure;
use Exception;
use Illuminate\Validation\Rule;
use Override;
use Spatie\LaravelData\Attributes\Hidden;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\Between;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\File;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

#[MergeValidationRules]
class StoreTorrentRequest extends Data
{
    public function __construct(
        #[File, Hidden]
        public UploadedFile $torrent,
        #[Max(255), Unique(Torrent::class, withoutTrashed: true)]
        public string $name,
        #[Max(65535)]
        public string $description,
        #[FromCurrentUserId('api')]
        public int $userId,
        #[Exists(Category::class, 'id')]
        public int $categoryId,
        #[Exists(Type::class, 'id')]
        public int $typeId,
        #[File, Hidden]
        public ?UploadedFile $nfo = null,
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
        public bool $sticky = false,
        #[Hidden]
        public bool $featured = false,
        #[Between(0, 100)]
        public int $free = 0,
        public bool $doubleUp = false,
        public bool $refundable = false,
        #[Date]
        public ?Carbon $duUntil = null,
        #[Date]
        public ?Carbon $flUntil = null,
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
        $properties['tmdb_movie_id'] = $properties['tmdb'] ?: null;
        $properties['tmdb_tv_id'] = $properties['tmdb'] ?: null;
        $properties['imdb'] = $properties['imdb'] ?: null;
        $properties['tvdb'] = $properties['tvdb'] ?: null;
        $properties['mal'] = $properties['mal'] ?: null;
        $properties['igdb'] = $properties['igdb'] ?: null;
        $properties['anon'] = $properties['anonymous'];
        $properties['personal_release'] = $properties['personal_release'] ?? false;
        $properties['du_until'] = \intval($properties['du_until']) ? now()->addDays(\intval($properties['du_until'])) : null;
        $properties['fl_until'] = \intval($properties['fl_until']) ? now()->addDays(\intval($properties['fl_until'])) : null;

        return $properties;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<Closure(string, mixed, Closure(string): never): void|\Illuminate\Validation\Rules\ProhibitedIf|\Illuminate\Validation\Rules\RequiredIf|\Illuminate\Validation\Rules\ExcludeIf|\Illuminate\Validation\ConditionalRules|\Illuminate\Validation\Rules\Unique|string>>
     */
    public function rules(Request $request): array
    {
        $user = $request->user()->loadExists('internals');
        $category = Category::query()->findOrFail($request->integer('category_id'));

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
                },
            ],
            'nfo' => [
                function (string $_attribute, mixed $value, Closure $fail): void {
                    if ($value->getClientOriginalExtension() !== 'nfo') {
                        $fail('The NFO uploaded does not have a ".nfo" file extension (it has "'.$value->getClientOriginalExtension().'"). Did you upload the correct file?');
                    }
                },
            ],
            'resolution_id' => [
                Rule::when($category->movie_meta || $category->tv_meta, 'required'),
                Rule::when(!$category->movie_meta && !$category->tv_meta, 'nullable'),
            ],
            'imdb' => [
                Rule::when($category->movie_meta || $category->tv_meta, [
                    'nullable',
                ]),
                Rule::excludeIf(!($category->movie_meta || $category->tv_meta)),
            ],
            'tvdb' => [
                Rule::when($category->tv_meta, [
                    'nullable',
                ]),
                Rule::excludeIf(!$category->tv_meta),
            ],
            'tmdb_movie_id' => [
                Rule::when($category->movie_meta, [
                    'nullable',
                ]),
                Rule::excludeIf(!$category->movie_meta),
            ],
            'tmdb_tv_id' => [
                Rule::when($category->tv_meta, [
                    'nullable',
                ]),
                Rule::excludeIf(!$category->tv_meta),
            ],
            'mal' => [
                Rule::when($category->movie_meta || $category->tv_meta, [
                    'nullable',
                ]),
                Rule::excludeIf(!($category->movie_meta || $category->tv_meta)),
            ],
            'igdb' => [
                Rule::when($category->game_meta, [
                    'nullable',
                ]),
                Rule::excludeIf(!$category->game_meta),
            ],
            'season_number' => [
                Rule::when($category->tv_meta, [
                    'required',
                ]),
                Rule::prohibitedIf(!$category->tv_meta),
            ],
            'episode_number' => [
                Rule::when($category->tv_meta, [
                    'required',
                ]),
                Rule::prohibitedIf(!$category->tv_meta),
            ],
            'internal' => [
                /** @phpstan-ignore property.notFound (Larastan doesn't yet support loadExists()) */
                Rule::excludeIf(!($user->group->is_modo || $user->internals_exists)),
            ],
            'sticky' => [
                /** @phpstan-ignore property.notFound (Larastan doesn't yet support loadExists()) */
                Rule::excludeIf(!($user->group->is_modo || $user->internals_exists)),
            ],
            'featured' => [
                /** @phpstan-ignore property.notFound (Larastan doesn't yet support loadExists()) */
                Rule::excludeIf(!($user->group->is_modo || $user->internals_exists)),
            ],
            'free' => [
                /** @phpstan-ignore property.notFound (Larastan doesn't yet support loadExists()) */
                Rule::excludeIf(!($user->group->is_modo || $user->internals_exists)),
            ],
            'doubleup' => [
                /** @phpstan-ignore property.notFound (Larastan doesn't yet support loadExists()) */
                Rule::excludeIf(!($user->group->is_modo || $user->internals_exists)),
            ],
            'refundable' => [
                /** @phpstan-ignore property.notFound (Larastan doesn't yet support loadExists()) */
                Rule::excludeIf(!($user->group->is_modo || $user->internals_exists)),
            ],
            'du_until' => [
                /** @phpstan-ignore property.notFound (Larastan doesn't yet support loadExists()) */
                Rule::excludeIf(!($user->group->is_modo || $user->internals_exists)),
            ],
            'fl_until' => [
                /** @phpstan-ignore property.notFound (Larastan doesn't yet support loadExists()) */
                Rule::excludeIf(!($user->group->is_modo || $user->internals_exists)),
            ],
        ];
    }
}
