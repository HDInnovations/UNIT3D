<?php
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
 * @credits    Rhilip <https://github.com/Rhilip> Roardom <roardom@protonmail.com>
 */

namespace App\Jobs;

use App\Models\FreeleechToken;
use App\Models\Peer;
use App\Models\PersonalFreeleech;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class ProcessAnnounce implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected $queries, protected $user, protected $group, protected $torrent)
    {
    }

    /**
     * Get the middleware the job should pass through.
     */
    public function middleware(): array
    {
        return [(new WithoutOverlapping($this->user->id.':'.$this->torrent->id))->releaseAfter(30)];
    }

    /**
     * Execute the job.
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function handle(): void
    {
        // Set Variables
        $realUploaded = $this->queries['uploaded'];
        $realDownloaded = $this->queries['downloaded'];
        $event = $this->queries['event'];
        $peerId = base64_decode($this->queries['peer_id']);
        $ipAddress = base64_decode($this->queries['ip-address']);

        // Get The Current Peer
        $peer = $this->torrent->peers
            ->where('peer_id', '=', $peerId)
            ->where('user_id', '=', $this->user->id)
            ->first();

        $uploaded = max($realUploaded - ($peer?->uploaded ?? 0), 0);
        $downloaded = max($realDownloaded >= ($peer?->downloaded ?? 0), 0);

        // If no Peer record found then create one
        if ($peer === null) {
            if ($this->queries['uploaded'] > 0 || $this->queries['downloaded'] > 0) {
                $event = 'started';
                $uploaded = 0;
                $downloaded = 0;
            }

            $peer = new Peer();
        }

        // Modification of Upload and Download (Check cache but in case redis data was lost hit DB)
        $personalFreeleech = cache()->rememberForever(
            'personal_freeleech:'.$this->user->id,
            fn () => PersonalFreeleech::query()
                ->where('user_id', '=', $this->user->id)
                ->exists()
        );

        $freeleechToken = cache()->rememberForever(
            'freeleech_token:'.$this->user->id.':'.$this->torrent->id,
            fn () => FreeleechToken::query()
                ->where('user_id', '=', $this->user->id)
                ->where('torrent_id', '=', $this->torrent->id)
                ->exists(),
        );

        if ($personalFreeleech ||
            $this->group->is_freeleech == 1 ||
            $freeleechToken ||
            config('other.freeleech') == 1) {
            $modDownloaded = 0;
        } elseif ($this->torrent->free >= 1) {
            // FL value in DB are from 0% to 100%.
            // Divide it by 100 and multiply it with "downloaded" to get discount download.
            $fl_discount = $downloaded * $this->torrent->free / 100;
            $modDownloaded = $downloaded - $fl_discount;
        } else {
            $modDownloaded = $downloaded;
        }

        if ($this->torrent->doubleup == 1 ||
            $this->group->is_double_upload == 1 ||
            config('other.doubleup') == 1) {
            $modUploaded = $uploaded * 2;
        } else {
            $modUploaded = $uploaded;
        }

        // Common Parts Extracted From Switch
        $peer->peer_id = $peerId;
        $peer->ip = $ipAddress;
        $peer->port = $this->queries['port'];
        $peer->agent = $this->queries['user-agent'];
        $peer->uploaded = $realUploaded;
        $peer->downloaded = $realDownloaded;
        $peer->seeder = $this->queries['left'] == 0;
        $peer->left = $this->queries['left'];
        $peer->torrent_id = $this->torrent->id;
        $peer->user_id = $this->user->id;
        $peer->updateConnectableStateIfNeeded();
        $peer->updated_at = now();

        switch ($event) {
            case 'started':
                $peer->active = true;

                break;
            case 'completed':
                $peer->active = true;

                // User Update
                if ($modUploaded > 0 || $modDownloaded > 0) {
                    $this->user->update([
                        'uploaded'   => DB::raw('uploaded + '.(int) $modUploaded),
                        'downloaded' => DB::raw('downloaded + '.(int) $modDownloaded),
                    ]);
                }
                // End User Update

                // Torrent Completed Update
                $this->torrent->times_completed += 1;

                break;
            case 'stopped':
                $peer->active = false;

                // User Update
                if ($modUploaded > 0 || $modDownloaded > 0) {
                    $this->user->update([
                        'uploaded'   => DB::raw('uploaded + '.(int) $modUploaded),
                        'downloaded' => DB::raw('downloaded + '.(int) $modDownloaded),
                    ]);
                }
                // End User Update
                break;
            default:
                $peer->active = true;

                // User Update
                if ($modUploaded > 0 || $modDownloaded > 0) {
                    $this->user->update([
                        'uploaded'   => DB::raw('uploaded + '.(int) $modUploaded),
                        'downloaded' => DB::raw('downloaded + '.(int) $modDownloaded),
                    ]);
                }
                // End User Update
        }

        Redis::connection('announce')->command('LPUSH', [
            config('cache.prefix').':peers:batch',
            serialize($peer->only([
                'peer_id',
                'ip',
                'port',
                'agent',
                'uploaded',
                'downloaded',
                'left',
                'seeder',
                'torrent_id',
                'user_id',
                'connectable',
                'active'
            ]))
        ]);

        Redis::connection('announce')->command('LPUSH', [
            config('cache.prefix').':histories:batch',
            serialize([
                'user_id'           => $this->user->id,
                'torrent_id'        => $this->torrent->id,
                'agent'             => $this->queries['user-agent'],
                'uploaded'          => $event === 'started' ? 0 : $modUploaded,
                'actual_uploaded'   => $event === 'started' ? 0 : $uploaded,
                'client_uploaded'   => $realUploaded,
                'downloaded'        => $event === 'started' ? 0 : $modDownloaded,
                'actual_downloaded' => $event === 'started' ? 0 : $downloaded,
                'client_downloaded' => $realDownloaded,
                'seeder'            => $this->queries['left'] == 0,
                'active'            => $event !== 'stopped',
                'seedtime'          => 0,
                'immune'            => $this->group->is_immune,
                'completed_at'      => $event === 'completed' ? now() : null,
            ])
        ]);

        $otherSeeders = $this
            ->torrent
            ->peers
            ->where('left', '=', 0)
            ->where('peer_id', '!=', $peerId)
            ->count();
        $otherLeechers = $this
            ->torrent
            ->peers
            ->where('left', '>', 0)
            ->where('peer_id', '!=', $peerId)
            ->count();

        $this->torrent->seeders = $otherSeeders + (int) ($this->queries['left'] == 0 && $this->queries['event'] !== 'stopped');
        $this->torrent->leechers = $otherLeechers + (int) ($this->queries['left'] > 0 && $this->queries['event'] !== 'stopped');

        $this->torrent->save();
    }
}
