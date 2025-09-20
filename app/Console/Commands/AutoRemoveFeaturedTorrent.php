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
 * @author     HDVinnie <hdinnovations@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Console\Commands;

use App\Models\FeaturedTorrent;
use App\Models\Torrent;
use App\Repositories\ChatRepository;
use App\Services\Unit3dAnnounce;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Throwable;

class AutoRemoveFeaturedTorrent extends Command
{
    /**
     * AutoRemoveFeaturedTorrent Constructor.
     */
    public function __construct(private readonly ChatRepository $chatRepository)
    {
        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:remove_featured_torrent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically removes featured torrents if expired';

    /**
     * Execute the console command.
     *
     * @throws Exception|Throwable If there is an error during the execution of the command.
     */
    final public function handle(): void
    {
        $current = Carbon::now();
        $featuredTorrents = FeaturedTorrent::with('torrent')->where('created_at', '<', $current->copy()->subDays(7))->get();

        foreach ($featuredTorrents as $featuredTorrent) {
            // Find The Torrent

            if ($featuredTorrent->torrent !== null) {
                $featuredTorrent->delete();

                // Auto Announce Featured Expired
                $appurl = config('app.url');

                $this->chatRepository->systemMessage(
                    \sprintf('Ladies and Gents, [url=%s/torrents/%s]%s[/url] is no longer featured.', $appurl, $featuredTorrent->torrent_id, $featuredTorrent->torrent->name)
                );

                Unit3dAnnounce::removeFeaturedTorrent($featuredTorrent->torrent_id);
            }

            // Delete The Record From DB
            $featuredTorrent->delete();
        }

        cache()->forget('featured-torrent-ids');

        $this->comment('Automated removal featured torrents command complete');
    }
}
