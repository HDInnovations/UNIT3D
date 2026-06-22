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
use App\Models\User;
use App\Repositories\ChatRepository;
use App\Services\Unit3dAnnounce;
use Exception;
use Illuminate\Console\Command;
use Throwable;

class AutoFeatureTorrent extends Command
{
    /**
     * AutoFeatureTorrent Constructor.
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
    protected $signature = 'auto:feature_torrent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically features a random torrent with exactly 1 seeder each day';

    /**
     * Execute the console command.
     *
     * @throws Exception|Throwable If there is an error during the execution of the command.
     */
    final public function handle(): void
    {
        $torrent = Torrent::where('seeders', '=', 1)
            ->whereDoesntHave('featured')
            ->inRandomOrder()
            ->first();

        if ($torrent === null) {
            $this->comment('No eligible torrent found with exactly 1 seeder.');

            return;
        }

        // Use the first admin/system user as the actor for this automated action
        $systemUser = User::find(1);

        if ($systemUser === null) {
            $this->error('System user (id=1) not found. Cannot feature torrent.');

            return;
        }

        Unit3dAnnounce::addFeaturedTorrent($torrent->id);

        $featured = new FeaturedTorrent();
        $featured->user_id = $systemUser->id;
        $featured->torrent_id = $torrent->id;
        $featured->save();

        cache()->forget('featured-torrent-ids');

        $appurl = config('app.url');

        $this->chatRepository->systemMessage(
            \sprintf('Ladies and Gents, [url=%s/torrents/%s]%s[/url] has been added to the Featured Torrents Slider! Grab It While You Can!', $appurl, $torrent->id, $torrent->name)
        );

        $this->comment(\sprintf('Torrent "%s" (id=%d) is now featured.', $torrent->name, $torrent->id));
    }
}
