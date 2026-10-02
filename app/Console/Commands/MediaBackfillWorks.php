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
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Console\Commands;

use App\Models\Scopes\ApprovedScope;
use App\Models\Torrent;
use App\Services\Media\MediaWorkCatalog;
use Illuminate\Console\Command;
use Throwable;

/**
 * Attaches every existing non-deleted torrent to its canonical catalogue
 * Work using only already-stored metadata (no outbound provider calls, no
 * jobs dispatched). Safe to run repeatedly at any time: already-attached
 * torrents are cheaply re-verified/refreshed rather than re-created, and
 * nothing about a torrent's infohash, history, or accounting is touched.
 */
class MediaBackfillWorks extends Command
{
    /**
     * @var string
     */
    protected $signature = 'media:backfill-works {--chunk=200 : Number of torrents to process per chunk}';

    /**
     * @var string
     */
    protected $description = 'Attach existing torrents to their canonical catalogue Work using only stored metadata';

    public function handle(MediaWorkCatalog $catalog): int
    {
        $chunkSize = max(1, (int) $this->option('chunk'));
        $processed = 0;
        $failed = 0;

        Torrent::query()
            ->withoutGlobalScope(ApprovedScope::class)
            ->with(['category', 'metadata'])
            ->orderBy('id')
            ->chunkById($chunkSize, function ($torrents) use ($catalog, &$processed, &$failed): void {
                foreach ($torrents as $torrent) {
                    try {
                        $catalog->sync($torrent);
                        $this->backfillEditionFacets($catalog, $torrent);
                    } catch (Throwable $exception) {
                        $failed++;
                        $this->warn("Failed to attach torrent #{$torrent->id}: {$exception->getMessage()}");

                        continue;
                    }

                    $processed++;
                }
            });

        $this->info("Backfilled {$processed} torrent(s) to their canonical Work.".($failed > 0 ? " {$failed} failed." : ''));

        return self::SUCCESS;
    }

    /**
     * Existing music/book TorrentMetadata rows predate the per-edition
     * `facets` column, so old catalogue entries need it computed here, from
     * the raw payload already stored, rather than requiring a provider
     * refetch just to become filterable by format/language/publisher.
     */
    private function backfillEditionFacets(MediaWorkCatalog $catalog, Torrent $torrent): void
    {
        $metadata = $torrent->getRelation('metadata');

        if ($metadata === null || $metadata->facets !== null) {
            return;
        }

        $kind = match ($metadata->source) {
            'musicbrainz'                    => 'music',
            'open-library', 'google-books'   => 'book',
            default                          => null,
        };

        if ($kind === null || !\is_array($metadata->raw)) {
            return;
        }

        $metadata->update(['facets' => $catalog->computeFacets($kind, $metadata->raw)]);
    }
}
