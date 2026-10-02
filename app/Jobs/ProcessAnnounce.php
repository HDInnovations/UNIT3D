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
use App\Models\PersonalFreeleech;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class ProcessAnnounce implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Number of times the job may be attempted. Concurrent announces for one
     * peer serialize on the `peer_announce_cursors` row lock inside the
     * transaction, so an attempt is spent only on a genuine downstream
     * failure, never on contention.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Fallback job UUID used only when this job is executed outside of a real
     * queue worker (no `$this->job` set, e.g. a direct `->handle()` call in a
     * test). Cached per instance so repeated calls on the same instance --
     * simulating a queue retrying the same dispatch -- see a stable UUID,
     * same as a real worker would via `$this->job->uuid()`.
     */
    private ?string $fallbackJobUuid = null;

    /**
     * Original HTTP receive time, before lookups or queue delivery. Older
     * requests must not reset a newer cursor when those stages reorder them.
     */
    public \Illuminate\Support\Carbon $receivedAt;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public AnnounceQueryDTO $queries,
        public AnnounceUserDTO $user,
        public AnnounceTorrentDTO $torrent,
        public bool $visible,
        \Illuminate\Support\Carbon $receivedAt,
    ) {
        $this->onQueue('tracker');
        $this->receivedAt = $receivedAt;
    }

    /**
     * Seconds to wait before each retry.
     *
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [5, 15, 30];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Stable across retries of this exact job dispatch (Laravel preserves
        // the job UUID when it re-releases a failed attempt), used both as the
        // transactional-outbox key on `peer_announce_cursors` and as the
        // per-row idempotency key the `auto:upsert_*` flush commands check
        // against `announce_job_receipts`.
        $jobUuid = $this->job?->uuid() ?? ($this->fallbackJobUuid ??= (string) Str::uuid());

        $peerId = $this->queries->getPeerId();

        // Set Variables
        $event = $this->queries->event;

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
                fn () => FeaturedTorrent::select('torrent_id')->pluck('torrent_id')->toArray(),
            ),
            true
        );

        // The connectable check opens a real socket (up to a 1s timeout), so
        // it must never run while the peer-accounting row lock below is held.
        $connectable = $this->getConnectableStatus();

        $receivedAt = $this->receivedAt;

        $cursorKey = [
            'user_id'    => $this->user->id,
            'torrent_id' => $this->torrent->id,
            'peer_id'    => $peerId,
        ];

        /**
         * @var array{0: array<string, mixed>|null, 1: array<string, mixed>|null, 2: array<string, mixed>|null}
         */
        [$peerPayload, $historyPayload, $announcePayload] = DB::transaction(function () use (
            $jobUuid,
            $peerId,
            $cursorKey,
            $event,
            $receivedAt,
            $personalFreeleech,
            $freeleechToken,
            $isFeatured,
            $connectable,
        ): array {
            $wasInserted = DB::table('peer_announce_cursors')->insertOrIgnore([[
                ...$cursorKey,
                'uploaded'    => 0,
                'downloaded'  => 0,
                'left'        => 0,
                'received_at' => '1970-01-01 00:00:00',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]]) > 0;

            $cursor = DB::table('peer_announce_cursors')
                ->where($cursorKey)
                ->lockForUpdate()
                ->first();

            // Exact retry of this same job attempt after it already
            // committed: redeliver the stored outbox payload verbatim,
            // never recompute against the now-advanced baseline.
            if ($cursor->pending_job_uuid === $jobUuid) {
                return [
                    $this->decodeOutboxPayload($cursor->pending_peer_payload),
                    $this->decodeOutboxPayload($cursor->pending_history_payload),
                    $cursor->pending_announce_payload === null ? null : $this->decodeOutboxPayload($cursor->pending_announce_payload),
                ];
            }

            // A different, earlier job attempt on this exact peer committed a
            // delta but never confirmed delivery (crashed between its RPUSH
            // and clearing the outbox). Drain it before doing anything else
            // so it is never silently clobbered and lost.
            if ($cursor->pending_job_uuid !== null) {
                $this->deliver(
                    $this->decodeOutboxPayload($cursor->pending_peer_payload),
                    $this->decodeOutboxPayload($cursor->pending_history_payload),
                    $cursor->pending_announce_payload === null ? null : $this->decodeOutboxPayload($cursor->pending_announce_payload),
                );

                // Clear it immediately (not only at the end of this method):
                // if this job turns out to be stale/superseded below, it must
                // not leave a dangling pointer that a later job would drain
                // (and redundantly redeliver) a second time.
                DB::table('peer_announce_cursors')->where($cursorKey)->update([
                    'pending_job_uuid'         => null,
                    'pending_peer_payload'     => null,
                    'pending_history_payload'  => null,
                    'pending_announce_payload' => null,
                ]);
            }

            // Queue retries/requeues do not preserve FIFO order: an older
            // announce can land and commit after a newer one already has. If
            // that happened, this job is superseded -- it must not recompute
            // credit (its bytes are stale) and must not touch the baseline
            // (which would regress it for the next, real announce). It is a
            // true no-op, not a clamp, so a legitimate client reset that
            // genuinely is the latest announce is still honoured.
            if ($receivedAt->lessThanOrEqualTo($cursor->received_at)) {
                return [null, null, null];
            }

            $isNewPeer = $wasInserted;

            // Calculate the change in upload/download compared to the last announce
            $uploadedDelta = max($this->queries->uploaded - (int) $cursor->uploaded, 0);
            $downloadedDelta = max($this->queries->downloaded - (int) $cursor->downloaded, 0);

            $localEvent = $event;

            // If no peer record found then set deltas to 0 and change to `started` event
            if ($isNewPeer) {
                if ($this->queries->uploaded > 0 || $this->queries->downloaded > 0) {
                    $localEvent = 'started';
                    $uploadedDelta = 0;
                    $downloadedDelta = 0;
                }
            }

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
            if (($creditedUploadedDelta > 0 || $creditedDownloadedDelta > 0) && $localEvent !== 'started') {
                DB::table('users')->where('id', '=', $this->user->id)->update([
                    'uploaded'   => DB::raw('uploaded + '.(int) $creditedUploadedDelta),
                    'downloaded' => DB::raw('downloaded + '.(int) $creditedDownloadedDelta),
                ]);
            }

            /**
             * Peer batch upsert payload.
             *
             * @see \App\Console\Commands\AutoUpsertPeers
             */
            $peerPayload = [
                'peer_id'     => $peerId,
                'ip'          => $this->queries->getIp(),
                'port'        => $this->queries->port,
                'agent'       => $this->queries->getAgent(),
                'uploaded'    => $this->queries->uploaded,
                'downloaded'  => $this->queries->downloaded,
                'left'        => $this->queries->left,
                'seeder'      => $this->queries->left === 0,
                'torrent_id'  => $this->torrent->id,
                'user_id'     => $this->user->id,
                'active'      => $localEvent !== 'stopped',
                'visible'     => $this->visible,
                'connectable' => $connectable,
                '_job_uuid'   => $jobUuid,
            ];

            /**
             * History batch upsert payload.
             *
             * @see \App\Console\Commands\AutoUpsertHistories
             */
            $historyPayload = [
                'user_id'           => $this->user->id,
                'torrent_id'        => $this->torrent->id,
                'agent'             => $this->queries->getAgent(),
                'uploaded'          => $localEvent === 'started' ? 0 : $creditedUploadedDelta,
                'actual_uploaded'   => $localEvent === 'started' ? 0 : $uploadedDelta,
                'client_uploaded'   => $this->queries->uploaded,
                'downloaded'        => $localEvent === 'started' ? 0 : $creditedDownloadedDelta,
                'actual_downloaded' => $localEvent === 'started' ? 0 : $downloadedDelta,
                'client_downloaded' => $this->queries->downloaded,
                'seeder'            => $this->queries->left === 0,
                'active'            => $localEvent !== 'stopped',
                'seedtime'          => 0,
                'immune'            => $this->user->isDonor ?: $this->user->group->isImmune,
                'completed_at'      => $localEvent === 'completed' ? now()->toDateTimeString() : null,
                '_job_uuid'         => $jobUuid,
            ];

            /**
             * Announce log batch insert payload.
             *
             * @see \App\Console\Commands\AutoUpsertAnnounces
             */
            $announcePayload = config('announce.log_announces') ? [
                'user_id'    => $this->user->id,
                'torrent_id' => $this->torrent->id,
                'uploaded'   => $this->queries->uploaded,
                'downloaded' => $this->queries->downloaded,
                'left'       => $this->queries->left,
                'corrupt'    => $this->queries->corrupt,
                'peer_id'    => $peerId,
                'port'       => $this->queries->port,
                'numwant'    => $this->queries->numwant,
                'event'      => $this->queries->event,
                'key'        => $this->queries->key,
                '_job_uuid'  => $jobUuid,
            ] : null;

            DB::table('peer_announce_cursors')
                ->where($cursorKey)
                ->update([
                    'uploaded'                 => $this->queries->uploaded,
                    'downloaded'               => $this->queries->downloaded,
                    'left'                     => $this->queries->left,
                    'received_at'              => $receivedAt->format('Y-m-d H:i:s.u'),
                    'pending_job_uuid'         => $jobUuid,
                    'pending_peer_payload'     => $this->encodeOutboxPayload($peerPayload),
                    'pending_history_payload'  => $this->encodeOutboxPayload($historyPayload),
                    'pending_announce_payload' => $announcePayload === null ? null : $this->encodeOutboxPayload($announcePayload),
                    'updated_at'               => now(),
                ]);

            return [$peerPayload, $historyPayload, $announcePayload];
        }, 5);

        // A superseded (out-of-order/stale) job has nothing to deliver.
        if ($peerPayload === null) {
            return;
        }

        $this->deliver($peerPayload, $historyPayload, $announcePayload);

        DB::table('peer_announce_cursors')
            ->where($cursorKey)
            ->where('pending_job_uuid', '=', $jobUuid)
            ->update([
                'pending_job_uuid'         => null,
                'pending_peer_payload'     => null,
                'pending_history_payload'  => null,
                'pending_announce_payload' => null,
            ]);
    }

    /**
     * RPUSH the peer/history/announce payloads onto their respective batch
     * lists for `auto:upsert_peers`/`auto:upsert_histories`/`auto:upsert_announces`.
     *
     * @param array<string, mixed>      $peerPayload
     * @param array<string, mixed>      $historyPayload
     * @param array<string, mixed>|null $announcePayload
     */
    private function deliver(array $peerPayload, array $historyPayload, ?array $announcePayload): void
    {
        Redis::connection('announce')->command('RPUSH', [
            config('cache.prefix').':peers:batch',
            serialize($peerPayload),
        ]);

        Redis::connection('announce')->command('RPUSH', [
            config('cache.prefix').':histories:batch',
            serialize($historyPayload),
        ]);

        if ($announcePayload !== null) {
            Redis::connection('announce')->command('RPUSH', [
                config('cache.prefix').':announces:batch',
                serialize($announcePayload),
            ]);
        }
    }

    /**
     * Encode a batch payload for durable storage in a `peer_announce_cursors`
     * `pending_*_payload` column. Base64-wrapped because the payload embeds
     * raw binary `peer_id`/`ip` fields that are not valid UTF-8.
     *
     * @param array<string, mixed> $payload
     */
    private function encodeOutboxPayload(array $payload): string
    {
        return base64_encode(serialize($payload));
    }

    /**
     * Reverse {@see self::encodeOutboxPayload()}.
     *
     * @return array<string, mixed>
     */
    private function decodeOutboxPayload(string $encoded): array
    {
        /** @var array<string, mixed> */
        return unserialize(base64_decode($encoded), ['allowed_classes' => false]);
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
