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
 * @credits    Rhilip <https://github.com/Rhilip> Roardom <roardom@protonmail.com>
 */

namespace App\Jobs;

use App\DTO\AnnounceQueryDTO;
use App\DTO\AnnounceTorrentDTO;
use App\DTO\AnnounceUserDTO;
use App\Models\FeaturedTorrent;
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
    public function __construct(
        public AnnounceQueryDTO $queries,
        public AnnounceUserDTO $user,
        public AnnounceTorrentDTO $torrent,
        public bool $visible,
    ) {
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return [new WithoutOverlapping($this->user->id.':'.$this->torrent->id)];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Set Variables
        $event = $this->queries->event;

        $peer = Peer::query()
            ->select(['active', 'left', 'uploaded', 'downloaded'])
            ->where('user_id', '=', $this->user->id)
            ->where('torrent_id', '=', $this->torrent->id)
            ->where('peer_id', '=', $this->queries->getPeerId())
            ->first();

        $isNewPeer = $peer === null;

        // Calculate the change in upload/download compared to the last announce
        $uploadedDelta = max($this->queries->uploaded - ($peer?->uploaded ?? 0), 0);
        $downloadedDelta = max($this->queries->downloaded - ($peer?->downloaded ?? 0), 0);

        // If no peer record found then set deltas to 0 and change to `started` event
        if ($isNewPeer) {
            if ($this->queries->uploaded > 0 || $this->queries->downloaded > 0) {
                $event = 'started';
                $uploadedDelta = 0;
                $downloadedDelta = 0;
            }
        }

        // Check if user currently has a personal freeleech
        $personalFreeleech = cache()->rememberForever(
            'personal_freeleech:'.$this->user->id,
            fn () => PersonalFreeleech::query()
                ->where('user_id', '=', $this->user->id)
                ->exists()
        );

        // Check if user has a freeleech token on this torrent
        $freeleechToken = cache()->rememberForever(
            'freeleech_token:'.$this->user->id.':'.$this->torrent->id,
            fn () => FreeleechToken::query()
                ->where('user_id', '=', $this->user->id)
                ->where('torrent_id', '=', $this->torrent->id)
                ->exists(),
        );

        // Check if the torrent is featured
        $isFeatured = \in_array(
            $this->torrent->id,
            cache()->rememberForever(
                'featured-torrent-ids',
                fn () => FeaturedTorrent::query()->select('torrent_id')->pluck('torrent_id')->toArray(),
            ),
            true
        );

        // Calculate credited Download
        if (
            $personalFreeleech
            || $this->user->isDonor
            || $this->user->group->isFreeleech
            || $freeleechToken
            || $isFeatured
            || config('other.freeleech')
        ) {
            $creditedDownloadedDelta = 0;
        } elseif ($this->torrent->percentFree >= 1) {
            // Freeleech values in the database are from 0 to 100
            // 0 means 0% of the bytes are freeleech, i.e. 100% of the bytes are counted.
            // 100 means 100% of the bytes are freeleech, i.e. 0% of the bytes are counted.
            // This means we have to subtract the value stored in the database from 100 before multiplying.
            // Also make sure that 100% is the highest value of freeleech possible
            // in order to not subtract download from an account.
            $creditedDownloadedDelta = $downloadedDelta * (100 - min(100, $this->torrent->percentFree)) / 100;
        } else {
            $creditedDownloadedDelta = $downloadedDelta;
        }

        // Calculate credited upload
        if (
            $this->torrent->isDoubleUpload
            || $this->user->group->isDoubleUpload
            || $isFeatured
            || config('other.doubleup')
        ) {
            $creditedUploadedDelta = $uploadedDelta * 2;
        } else {
            $creditedUploadedDelta = $uploadedDelta;
        }

        // User Updates
        if (($creditedUploadedDelta > 0 || $creditedDownloadedDelta > 0) && $event !== 'started') {
            DB::table('users')->where('id', '=', $this->user->id)->update([
                'uploaded'   => DB::raw('uploaded + '.(int) $creditedUploadedDelta),
                'downloaded' => DB::raw('downloaded + '.(int) $creditedDownloadedDelta),
            ]);
        }

        // Peer updates

        /**
         * Peer batch upsert.
         *
         * @see \App\Console\Commands\AutoUpsertPeers
         */
        Redis::connection('announce')->command('RPUSH', [
            config('cache.prefix').':peers:batch',
            serialize([
                'peer_id'     => $this->queries->getPeerId(),
                'ip'          => $this->queries->getIp(),
                'port'        => $this->queries->port,
                'agent'       => $this->queries->getAgent(),
                'uploaded'    => $this->queries->uploaded,
                'downloaded'  => $this->queries->downloaded,
                'left'        => $this->queries->left,
                'seeder'      => $this->queries->left === 0,
                'torrent_id'  => $this->torrent->id,
                'user_id'     => $this->user->id,
                'active'      => $event !== 'stopped',
                'visible'     => $this->visible,
                'connectable' => $this->getConnectableStatus(),
            ]),
        ]);

        // History updates

        /**
         * History batch upsert.
         *
         * @see \App\Console\Commands\AutoUpsertHistories
         */
        Redis::connection('announce')->command('RPUSH', [
            config('cache.prefix').':histories:batch',
            serialize([
                'user_id'           => $this->user->id,
                'torrent_id'        => $this->torrent->id,
                'agent'             => $this->queries->getAgent(),
                'uploaded'          => $event === 'started' ? 0 : $creditedUploadedDelta,
                'actual_uploaded'   => $event === 'started' ? 0 : $uploadedDelta,
                'client_uploaded'   => $this->queries->uploaded,
                'downloaded'        => $event === 'started' ? 0 : $creditedDownloadedDelta,
                'actual_downloaded' => $event === 'started' ? 0 : $downloadedDelta,
                'client_downloaded' => $this->queries->downloaded,
                'seeder'            => $this->queries->left === 0,
                'active'            => $event !== 'stopped',
                'seedtime'          => 0,
                'immune'            => $this->user->isDonor ?: $this->user->group->isImmune,
                'completed_at'      => $event === 'completed' ? now() : null,
            ])
        ]);

        if (config('announce.log_announces')) {
            /**
             * Announce batch upsert.
             *
             * @see \App\Console\Commands\AutoUpsertAnnounces
             */
            Redis::connection('announce')->command('RPUSH', [
                config('cache.prefix').':announces:batch',
                serialize([
                    'user_id'    => $this->user->id,
                    'torrent_id' => $this->torrent->id,
                    'uploaded'   => $this->queries->uploaded,
                    'downloaded' => $this->queries->downloaded,
                    'left'       => $this->queries->left,
                    'corrupt'    => $this->queries->corrupt,
                    'peer_id'    => $this->queries->getPeerId(),
                    'port'       => $this->queries->port,
                    'numwant'    => $this->queries->numwant,
                    'event'      => $this->queries->event,
                    'key'        => $this->queries->key,
                ])
            ]);
        }
    }

    /**
     * Check if peer is connectable.
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    private function getConnectableStatus(): bool
    {
        if (!config('announce.connectable_check')) {
            return false;
        }

        $ip = $this->queries->getIp();

        // Pack
        $ip = inet_ntop(pack('A'.\strlen($ip), $ip));

        if ($ip === false) {
            return false;
        }

        // IPv6 Check
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $ip = '['.$ip.']';
        }

        $key = $ip.'-'.$this->queries->port.'-'.$this->queries->getAgent();

        // Check cache
        if (cache()->has('peers:connectable-timer:'.$key)) {
            return cache()->get('peers:connectable:'.$key) === true;
        }

        // Connect
        $connection = @fsockopen($ip, $this->queries->port, $_, $_, 1);

        if ($connectable = \is_resource($connection)) {
            fclose($connection);
        }

        cache()->put('peers:connectable:'.$key, $connectable, config('announce.connectable_check_interval'));
        cache()->remember('peers:connectable-timer:'.$key, config('announce.connectable_check_interval'), fn () => true);

        return $connectable;
    }
}
