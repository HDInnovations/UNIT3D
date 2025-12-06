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
 * @author     Obi-wana
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Console\Commands;

use App\Notifications\NewUploadContestWinner;
use App\Models\Torrent;
use App\Models\UploadContest;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class AutoRewardUploadContestPrize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:reward_upload_contest_prize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically hands out rewards for upload contests';

    /**
     * Execute the console command.
     *
     * @throws Exception|Throwable If there is an error during the execution of the command.
     */
    final public function handle(): void
    {
        // Get all active upload contests
        $activeUploadContests = UploadContest::where('ends_at', '<', now())
            ->where('awarded', '=', 0)
            ->get();

        foreach ($activeUploadContests as $activeUploadContest) {
            // Get the amount of competitors to reward based on prizes for the event
            $numRewards = $activeUploadContest->prizes->count();

            // Get prizes ordered by position
            $rewards = $activeUploadContest->prizes->sortBy('position');

            // Get top N competitors
            $winners = Torrent::query()
                ->with('user.group')
                ->where('anon', '=', false)
                ->select(DB::raw('user_id, count(*) as value'))
                ->where('created_at', '>=', $activeUploadContest->starts_at)
                ->where('created_at', '<=', $activeUploadContest->ends_at)
                ->groupBy('user_id')
                ->orderByDesc('value')
                ->limit($numRewards)
                ->get();

            foreach ($winners as $i => $winner) {
                $rewardsByPosition = $rewards->where('position', $i + 1);

                foreach ($rewardsByPosition as $reward) {
                    // Reward prize
                    if ($reward->type === 'bon') {
                        $winner->user->increment('seedbonus', $reward->amount);
                    }

                    if ($reward->type === 'fl_tokens') {
                        $winner->user->increment('fl_tokens', $reward->amount);
                    }
                }

                // Send notification
                $winner->user->notify(new NewUploadContestWinner($activeUploadContest));
            }

            // Set upload contest as awarded
            $activeUploadContest->update([
                'awarded' => true,
            ]);
        }

        $this->comment('Automated reward upload contest command complete');
    }
}
