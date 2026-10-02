<?php

declare(strict_types=1);

/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D
 *
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 * @author     HDVinnie
 */

namespace App\Jobs;

use App\Enums\GlobalRateLimit;
use App\Models\IgdbCompany;
use App\Models\IgdbGame;
use App\Models\IgdbGenre;
use App\Models\IgdbPlatform;
use App\Models\Scopes\ApprovedScope;
use App\Models\Torrent;
use App\Services\Igdb\IgdbClient;
use App\Services\Media\MediaWorkCatalog;
use Carbon\CarbonImmutable;
use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\Middleware\Skip;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;

class ProcessIgdbGameJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * ProcessIgdbGameJob constructor.
     */
    public function __construct(public int $id)
    {
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return [
            Skip::when(cache()->has("igdb-game-scraper:{$this->id}")),
            new WithoutOverlapping((string) $this->id)->dontRelease()->expireAfter(30),
            new RateLimited(GlobalRateLimit::IGDB),
        ];
    }

    /**
     * Determine the time at which the job should timeout.
     */
    public function retryUntil(): DateTime
    {
        return now()->addDay();
    }

    public function handle(IgdbClient $igdb): void
    {
        $fetchedGame = $igdb->game($this->id);

        IgdbGame::query()->upsert([[
            'id'                     => $this->id,
            'name'                   => $fetchedGame['name'] ?? null,
            'summary'                => $fetchedGame['summary'] ?? '',
            'first_artwork_image_id' => $fetchedGame['artworks'][0]['image_id'] ?? null,
            'first_release_date'     => is_int($fetchedGame['first_release_date'] ?? null)
                ? CarbonImmutable::createFromTimestampUTC($fetchedGame['first_release_date'])->format('Y-m-d H:i:s')
                : null,
            'cover_image_id'         => $fetchedGame['cover']['image_id'] ?? null,
            'url'                    => $fetchedGame['url'] ?? null,
            'rating'                 => $fetchedGame['rating'] ?? null,
            'rating_count'           => $fetchedGame['rating_count'] ?? null,
            'first_video_video_id'   => $fetchedGame['videos'][0]['video_id'] ?? null,
            'raw'                    => json_encode($fetchedGame, JSON_THROW_ON_ERROR),
        ]], ['id']);

        $game = IgdbGame::query()->findOrFail($this->id);

        $genres = [];

        foreach ($fetchedGame['genres'] ?? [] as $genre) {
            if (!is_array($genre)) {
                continue;
            }

            $id = $genre['id'] ?? null;
            $name = $genre['name'] ?? null;

            if ($id === null || $name === null) {
                continue;
            }

            $genres[] = [
                'id'   => $id,
                'name' => $name,
            ];
        }

        IgdbGenre::query()->upsert($genres, ['id']);
        $game->genres()->sync(array_unique(array_column($genres, 'id')));

        $platforms = [];

        foreach ($fetchedGame['platforms'] ?? [] as $platform) {
            if (!is_array($platform)) {
                continue;
            }

            $id = $platform['id'] ?? null;
            $name = $platform['name'] ?? null;

            if ($id === null || $name === null) {
                continue;
            }

            $platforms[] = [
                'id'                     => $id,
                'name'                   => $name,
                'platform_logo_image_id' => $platform['platform_logo']['image_id'] ?? null,
            ];
        }

        IgdbPlatform::query()->upsert($platforms, ['id']);
        $game->platforms()->sync(array_unique(array_column($platforms, 'id')));

        $companies = [];

        foreach ($fetchedGame['involved_companies'] ?? [] as $company) {
            if (!is_array($company)) {
                continue;
            }

            $companyData = $company['company'] ?? [];

            if (!is_array($companyData)) {
                continue;
            }

            $id = $companyData['id'] ?? null;
            $name = $companyData['name'] ?? null;

            if ($id === null || $name === null) {
                continue;
            }

            $companies[] = [
                'id'            => $id,
                'name'          => $name,
                'url'           => $companyData['url'] ?? null,
                'logo_image_id' => $companyData['logo']['image_id'] ?? null,
            ];
        }

        IgdbCompany::query()->upsert($companies, ['id']);
        $game->companies()->sync(array_unique(array_column($companies, 'id')));

        $torrents = Torrent::query()
            ->withoutGlobalScope(ApprovedScope::class)
            ->where('igdb', '=', $this->id)
            ->whereRelation('category', 'game_meta', '=', true)
            ->with('category')
            ->get();

        $catalog = app(MediaWorkCatalog::class);

        foreach ($torrents as $torrent) {
            $catalog->sync($torrent);
        }

        Torrent::query()
            ->whereIntegerInRaw('id', $torrents->pluck('id'))
            ->searchable();

        // Although IGDB doesn't publicly state they cache their api responses,
        // use the same value as tmdb to not abuse them with too many requests

        cache()->put("igdb-game-scraper:{$this->id}", now(), 8 * 3600);
    }
}
