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
use App\Helpers\Bencode;
use App\Helpers\TorrentTools;
use App\Models\Category;
use App\Models\Distributor;
use App\Models\Region;
use App\Models\Resolution;
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
use Spatie\LaravelData\Attributes\MapInputName;
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
        #[MapInputName('anonymous')]
        public bool $anon = false,
        public bool $personalRelease = false,
        public bool $internal = false,
        public bool $sticky = false,
        #[Hidden]
        public bool $featured = false,
        #[Between(0, 100)]
        public int $free = 0,
        public bool $doubleup = false,
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
        if (!(auth()->user()->group->is_modo || auth()->user()->internals()->exists())) {
            unset($properties['internal'], $properties['sticky'], $properties['featured'], $properties['free'], $properties['doubleup'], $properties['refundable'], $properties['du_until'], $properties['fl_until']);
        }

        $category = Category::query()->findOrFail((int) ($properties['category_id'] ?? 0));

        $properties['tmdb_movie_id'] = $category->movie_meta ? (($properties['tmdb'] ?? 0) ?: null) : null;
        $properties['tmdb_tv_id'] = $category->tv_meta ? (($properties['tmdb'] ?? 0) ?: null) : null;
        $properties['tvdb'] = $category->tv_meta ? (($properties['tvdb'] ?? 0) ?: null) : null;
        $properties['imdb'] = ($category->movie_meta || $category->tv_meta) ? (($properties['imdb'] ?? 0) ?: null) : null;
        $properties['mal'] = ($category->movie_meta || $category->tv_meta) ? (($properties['mal'] ?? 0) ?: null) : null;
        $properties['igdb'] = $category->game_meta ? (($properties['igdb'] ?? 0) ?: null) : null;
        $properties['du_until'] = \array_key_exists('du_until', $properties) && \intval($properties['du_until']) ? now()->addDays(\intval($properties['du_until'])) : null;
        $properties['fl_until'] = \array_key_exists('fl_until', $properties) && \intval($properties['fl_until']) ? now()->addDays(\intval($properties['fl_until'])) : null;

        return $properties;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<Closure(string, mixed, Closure(string): never): void|\Illuminate\Validation\Rules\ProhibitedIf|\Illuminate\Validation\Rules\RequiredIf|\Illuminate\Validation\Rules\ExcludeIf|\Illuminate\Validation\ConditionalRules|\Illuminate\Validation\Rules\Unique|string>>
     */
    public static function rules(Request $request): array
    {
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

                    foreach (array_column(TorrentTools::getTorrentFiles($decodedTorrent), 'name') as $path) {
                        if (!TorrentTools::isValidPath($path)) {
                            $fail('Invalid filepath in torrent files');
                        }
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
        ];
    }
}
