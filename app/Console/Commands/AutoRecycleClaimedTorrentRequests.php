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

use App\Models\TorrentRequestClaim;
use App\Repositories\ChatRepository;
use Illuminate\Console\Command;
use Exception;
use Throwable;

class AutoRecycleClaimedTorrentRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:recycle_claimed_torrent_requests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recycle torrent requests that were claimed but not filled within 7 days.';

    /**
     * AutoRecycleClaimedTorrentRequests Constructor.
     */
    public function __construct(private readonly ChatRepository $chatRepository)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @throws Exception|Throwable If there is an error during the execution of the command.
     */
    final public function handle(): void
    {
        TorrentRequestClaim::query()
            ->with('request')
            ->where('created_at', '<', now()->subDays(7))
            ->whereHas(
                'request',
                fn ($query) => $query
                    ->whereNull('filled_by')
                    ->whereNull('filled_when')
                    ->whereNull('torrent_id')
            )
            ->chunkById(100, function ($claims): void {
                foreach ($claims as $claim) {
                    $trUrl = href_request($claim->request);

                    $this->chatRepository->systemMessage(
                        \sprintf('[url=%s]%s[/url] claim has been reset due to not being filled within 7 days.', $trUrl, $claim->request->name)
                    );

                    $claim->delete();
                }
            });

        $this->comment('Automated request claim reset command complete');
    }
}
