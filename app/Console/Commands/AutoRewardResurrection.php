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

use App\Models\Resurrection;
use App\Notifications\ResurrectionCompleted;
use App\Repositories\ChatRepository;
use App\Services\Unit3dAnnounce;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Throwable;

class AutoRewardResurrection extends Command
{
    /**
     * AutoRewardResurrection Constructor.
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
    protected $signature = 'auto:reward_resurrection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically hands out rewards for successful resurrections';

    /**
     * Execute the console command.
     *
     * @throws Exception|Throwable If there is an error during the execution of the command.
     */
    final public function handle(): void
    {
        Resurrection::query()
            ->with(['torrent', 'user'])
            ->where('rewarded', '=', false)
            ->has('user')
            ->whereHas(
                'torrent.history',
                fn ($query) => $query
                    ->whereColumn('resurrections.user_id', '=', 'history.user_id')
                    ->whereColumn('history.seedtime', '>=', 'resurrections.seedtime')
            )
            ->chunk(100, function ($resurrections): void {
                foreach ($resurrections as $resurrection) {
                    $resurrection->update(['rewarded' => true]);

                    $resurrection->user->increment('fl_tokens', (int) config('graveyard.reward'));

                    // Auto Shout
                    $appurl = config('app.url');

                    $this->chatRepository->systemMessage(
                        \sprintf('Ladies and Gents, [url=%s/users/%s]%s[/url] has successfully resurrected [url=%s/torrents/%s]%s[/url].', $appurl, $resurrection->user->username, $resurrection->user->username, $appurl, $resurrection->torrent->id, $resurrection->torrent->name)
                    );

                    // Bump Torrent With FL
                    $torrentUrl = href_torrent($resurrection->torrent);

                    $resurrection->torrent->update([
                        'bumped_at' => Carbon::now(),
                        'free'      => 100,
                        'fl_until'  => Carbon::now()->addDays(3),
                    ]);

                    $this->chatRepository->systemMessage(
                        \sprintf('Ladies and Gents, [url=%s]%s[/url] has been granted 100%% FreeLeech for 3 days and has been bumped to the top.', $torrentUrl, $resurrection->torrent->name)
                    );

                    cache()->forget('announce-torrents:by-infohash:'.$resurrection->torrent->info_hash);

                    Unit3dAnnounce::addTorrent($resurrection->torrent);

                    // Send Private Message
                    $resurrection->user->notify(new ResurrectionCompleted($resurrection->torrent));
                }
            });

        $this->comment('Automated reward resurrections command complete');
    }
}
