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

namespace App\Console\Commands\Capacity;

use App\Enums\ModerationStatus;
use App\Models\Category;
use App\Models\Group;
use App\Models\Resolution;
use App\Models\Type;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

/**
 * Populates the isolated capacity-benchmark stack with a deterministic,
 * minimal-but-real catalogue: approved torrents (each linked to its own
 * `media_works` row, since `App\Http\Livewire\TorrentSearch` groups the
 * catalogue/search by `media_work_id` and explicitly filters
 * `media_work_id IS NOT NULL` in Meilisearch -- a torrent without one is
 * invisible to every warm web roundtrip this harness exercises) with valid
 * category/type/resolution/user foreign keys, normal (non-banned/non-
 * disabled/non-validating) users who can download, and a bounded pool of
 * pre-existing peers (with matching `peer_announce_cursors` baselines) so
 * announces look like a torrent that has been live for a while.
 *
 * One dedicated user/torrent/peer combination (never reused by the general
 * pool) is reserved as the "canary" for `capacity:verify-canary`.
 *
 * Refuses to run anywhere except the dedicated `capacity` environment so it
 * can never seed synthetic records into a real database by accident.
 */
final class SeedCapacityFixtures extends Command
{
    /** @var string */
    protected $signature = 'capacity:seed-fixtures
        {--torrents=10000 : Number of general-pool torrents to create (excludes the canary torrent)}
        {--users=10000 : Number of general-pool users to create (excludes the canary user)}
        {--peers=40000 : Number of pre-existing peers to create across the general torrent pool}
        {--seed=1337 : Deterministic seed (affects derived ids/hashes only, not FK assignment)}
        {--fresh : Wipe every previously seeded capacity fixture table (and their FK children) first}';

    /** @var string */
    protected $description = 'Seed deterministic catalogue/user/peer fixtures for the isolated capacity-benchmark stack only';

    private const EPOCH = '1970-01-01 00:00:00.000000';

    public function handle(): int
    {
        if (config('app.env') !== 'capacity') {
            $this->components->error(
                'Refusing to run outside the isolated `capacity` environment (APP_ENV='.config('app.env').'). '
                .'This command is benchmark-only and must never touch a real database.'
            );

            return self::FAILURE;
        }

        $torrentCount = (int) $this->option('torrents');
        $userCount = (int) $this->option('users');
        $peerCount = (int) $this->option('peers');
        $seed = (int) $this->option('seed');

        if ($this->option('fresh')) {
            $this->components->task('Wiping previously seeded fixture tables (FK children first)', function (): void {
                DB::statement('SET FOREIGN_KEY_CHECKS=0');
                foreach ([
                    'peer_announce_cursors', 'announce_job_receipts', 'peers', 'history',
                    'torrent_downloads', 'torrent_files', 'featured_torrents',
                    'torrents', 'media_works', 'users',
                ] as $table) {
                    if (DB::getSchemaBuilder()->hasTable($table)) {
                        DB::table($table)->truncate();
                    }
                }
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            });
        }

        // Hashing bcrypt is deliberately slow; compute the single shared
        // fixture password hash once instead of ~10k times.
        $sharedPasswordHash = Hash::make('capacity-local-only');

        $memberGroupId = Group::query()->where('slug', '=', 'user')->value('id');

        if ($memberGroupId === null) {
            throw new RuntimeException('Group `user` not found; run the base seeders (php artisan db:seed) first.');
        }

        $categoryIds = Category::query()->pluck('id')->all();
        $typeIds = Type::query()->pluck('id')->all();
        $resolutionIds = Resolution::query()->pluck('id')->all();

        if ($categoryIds === [] || $typeIds === [] || $resolutionIds === []) {
            throw new RuntimeException('Categories/types/resolutions not found; run the base seeders (php artisan db:seed) first.');
        }

        $this->components->info("Seeding {$userCount} users, {$torrentCount} torrents (+1 dedicated canary user/torrent each), {$peerCount} peers (seed={$seed})");

        // --- Canary user/torrent/work (seeded first, outside every modulo
        // cycle the general pool uses below, so the swarm scenario can never
        // coincidentally pick the canary's identity) -------------------------
        $canaryUserId = DB::table('users')->insertGetId([
            'username'          => 'capacity_canary_user',
            'email'             => 'capacity_canary_user@capacity.test',
            'email_verified_at' => now(),
            'password'          => $sharedPasswordHash,
            'passkey'           => substr(hash('sha256', 'capacity-canary-passkey-'.$seed), 0, 32),
            'group_id'          => $memberGroupId,
            'uploaded'          => 0,
            'downloaded'        => 0,
            'can_download'      => true,
            'can_chat'          => true,
            'can_comment'       => true,
            'can_request'       => true,
            'can_invite'        => true,
            'can_upload'        => true,
            'read_rules'        => true,
            'rsskey'            => hash('sha256', 'capacity-canary-rss-'.$seed),
            'api_token'         => hash('sha256', 'capacity-canary-api-'.$seed),
            'remember_token'    => substr(hash('sha256', 'capacity-canary-rt-'.$seed), 0, 10),
            'seedbonus'         => 0,
            'fl_tokens'         => 0,
            'invites'           => 0,
            'hitandruns'        => 0,
            'own_flushes'       => false,
            'last_login'        => now(),
            'last_action'       => now(),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        $canaryWorkId = DB::table('media_works')->insertGetId([
            'identity_key' => 'capacity-canary-work-'.$seed,
            'kind'         => 'movie',
            'title'        => 'Capacity Canary Work',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        $canaryTorrentId = DB::table('torrents')->insertGetId([
            'name'            => 'Capacity Canary Torrent',
            'description'     => 'Dedicated canary torrent, never touched by the general swarm pool.',
            'mediainfo'       => null,
            'info_hash'       => hash('sha1', 'capacity-canary-info-hash-'.$seed, true),
            'file_name'       => 'capacity-canary-torrent.bin',
            'num_file'        => 1,
            'size'            => 1_073_741_824,
            'nfo'             => '',
            'leechers'        => 0,
            'seeders'         => 0,
            'times_completed' => 0,
            'category_id'     => $categoryIds[0],
            'user_id'         => $canaryUserId,
            'imdb'            => 0,
            'tvdb'            => 0,
            'tmdb_movie_id'   => null,
            'tmdb_tv_id'      => null,
            'mal'             => 0,
            'igdb'            => 0,
            'type_id'         => $typeIds[0],
            'resolution_id'   => $resolutionIds[0],
            'media_work_id'   => $canaryWorkId,
            'free'            => '0',
            'doubleup'        => false,
            'highspeed'       => false,
            'status'          => ModerationStatus::APPROVED->value,
            'moderated_at'    => now(),
            'moderated_by'    => User::SYSTEM_USER_ID,
            'anon'            => false,
            'sticky'          => false,
            'internal'        => false,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        $canaryPasskey = DB::table('users')->where('id', $canaryUserId)->value('passkey');
        $canaryInfoHash = DB::table('torrents')->where('id', $canaryTorrentId)->value('info_hash');
        $canaryPeerId = str_repeat('C', 20);

        $canary = [
            'username'   => 'capacity_canary_user',
            'passkey'    => $canaryPasskey,
            'info_hash'  => bin2hex($canaryInfoHash),
            'peer_id'    => bin2hex($canaryPeerId),
            'user_id'    => $canaryUserId,
            'torrent_id' => $canaryTorrentId,
        ];

        // --- General-pool users ----------------------------------------------
        $bar = $this->output->createProgressBar($userCount);
        $bar->setMessage('users');

        foreach (array_chunk(range(1, $userCount), 500) as $chunk) {
            $rows = [];

            foreach ($chunk as $i) {
                $rows[] = [
                    'username'          => 'capacity_user_'.$i,
                    'email'             => 'capacity_user_'.$i.'@capacity.test',
                    'email_verified_at' => now(),
                    'password'          => $sharedPasswordHash,
                    'passkey'           => substr(hash('sha256', 'capacity-user-passkey-'.$i.'-'.$seed), 0, 32),
                    'group_id'          => $memberGroupId,
                    'uploaded'          => 0,
                    'downloaded'        => 0,
                    'can_download'      => true,
                    'can_chat'          => true,
                    'can_comment'       => true,
                    'can_request'       => true,
                    'can_invite'        => true,
                    'can_upload'        => true,
                    'read_rules'        => true,
                    'rsskey'            => hash('sha256', 'capacity-rss-'.$i),
                    'api_token'         => hash('sha256', 'capacity-api-'.$i),
                    'remember_token'    => substr(hash('sha256', 'capacity-rt-'.$i), 0, 10),
                    'seedbonus'         => 0,
                    'fl_tokens'         => 0,
                    'invites'           => 0,
                    'hitandruns'        => 0,
                    'own_flushes'       => false,
                    'last_login'        => now(),
                    'last_action'       => now(),
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ];
            }

            DB::table('users')->insert($rows);
            $bar->advance(\count($chunk));
        }
        $bar->finish();
        $this->newLine();

        $userIdList = array_values(DB::table('users')->where('username', 'like', 'capacity_user_%')->orderBy('id')->pluck('id')->all());

        // --- General-pool media works + torrents (1:1, required for the
        // catalogue/search grouping to ever show these torrents) -------------
        $bar = $this->output->createProgressBar($torrentCount);
        $bar->setMessage('media works');

        foreach (array_chunk(range(1, $torrentCount), 1000) as $chunk) {
            $rows = [];

            foreach ($chunk as $i) {
                $rows[] = [
                    'identity_key' => 'capacity-work-'.$i.'-'.$seed,
                    'kind'         => 'movie',
                    'title'        => 'Capacity Benchmark Work '.$i,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];
            }

            DB::table('media_works')->insert($rows);
            $bar->advance(\count($chunk));
        }
        $bar->finish();
        $this->newLine();

        $workIdList = array_values(DB::table('media_works')->where('identity_key', 'like', 'capacity-work-%-'.$seed)->orderBy('id')->pluck('id')->all());

        $bar = $this->output->createProgressBar($torrentCount);
        $bar->setMessage('torrents');

        foreach (array_chunk(range(1, $torrentCount), 500) as $chunk) {
            $rows = [];

            foreach ($chunk as $i) {
                $ownerId = $userIdList[($i - 1) % \count($userIdList)];

                $rows[] = [
                    'name'            => 'Capacity Benchmark Torrent '.$i,
                    'description'     => 'Deterministic fixture torrent #'.$i.' generated by capacity:seed-fixtures.',
                    'mediainfo'       => null,
                    'info_hash'       => hash('sha1', 'capacity-info-hash-'.$i.'-'.$seed, true),
                    'file_name'       => 'capacity-torrent-'.$i.'.bin',
                    'num_file'        => 1,
                    'size'            => 1_073_741_824, // 1 GiB nominal
                    'nfo'             => '',
                    'leechers'        => 0,
                    'seeders'         => 0,
                    'times_completed' => 0,
                    'category_id'     => $categoryIds[$i % \count($categoryIds)],
                    'user_id'         => $ownerId,
                    'imdb'            => 0,
                    'tvdb'            => 0,
                    'tmdb_movie_id'   => null,
                    'tmdb_tv_id'      => null,
                    'mal'             => 0,
                    'igdb'            => 0,
                    'type_id'         => $typeIds[$i % \count($typeIds)],
                    'resolution_id'   => $resolutionIds[$i % \count($resolutionIds)],
                    'media_work_id'   => $workIdList[$i - 1],
                    'free'            => '0',
                    'doubleup'        => false,
                    'highspeed'       => false,
                    'status'          => ModerationStatus::APPROVED->value,
                    'moderated_at'    => now(),
                    'moderated_by'    => User::SYSTEM_USER_ID,
                    'anon'            => false,
                    'sticky'          => false,
                    'internal'        => false,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ];
            }

            DB::table('torrents')->insert($rows);
            $bar->advance(\count($chunk));
        }
        $bar->finish();
        $this->newLine();

        $torrentIdList = array_values(DB::table('torrents')->where('name', 'like', 'Capacity Benchmark Torrent %')->orderBy('id')->pluck('id')->all());

        // Index-aligned with $userIdList/$torrentIdList (both ordered by id)
        // so `$allPasskeys[$userIdx]`/`$allInfoHashesHex[$torrentIdx]` below
        // give the exact real passkey/info_hash for that peer's owner/
        // torrent, with no extra per-peer query.
        $allPasskeys = DB::table('users')->where('username', 'like', 'capacity_user_%')->orderBy('id')->pluck('passkey')->all();
        $allInfoHashesHex = array_map('bin2hex', DB::table('torrents')->where('name', 'like', 'Capacity Benchmark Torrent %')->orderBy('id')->pluck('info_hash')->all());

        // --- Pre-existing peers (so announces return a realistic swarm),
        // plus their required peer_announce_cursors baseline ------------------
        $bar = $this->output->createProgressBar($peerCount);
        $bar->setMessage('peers');

        $userCountN = \count($userIdList);
        $torrentCountN = \count($torrentIdList);
        $peerManifestRows = [];

        foreach (array_chunk(range(0, $peerCount - 1), 1000) as $chunk) {
            $rows = [];

            foreach ($chunk as $i) {
                // `AnnounceController::checkMaxConnections` rejects a user
                // announcing more than `announce.rate_limit` (3) *active*
                // peer locations on the SAME torrent. A naive `i % users` /
                // `i % torrents` with users == torrents == 10000 would give
                // every (user, torrent) pair exactly `peerCount / userCount`
                // peers (4 at the 40000/10000 default) -- over the limit.
                // Offsetting torrentIdx by the repeat round guarantees each
                // (userIdx, torrentIdx) pair is unique across the whole pool
                // whenever peerCount is a multiple of userCount, so every
                // real pair gets exactly one peer.
                $userIdx = $i % $userCountN;
                $round = intdiv($i, $userCountN);
                $torrentIdx = ($userIdx + $round) % $torrentCountN;

                $torrentId = $torrentIdList[$torrentIdx];
                $userId = $userIdList[$userIdx];
                $seeder = $i % 3 === 0;

                $peerIdRaw = substr(hash('sha1', 'capacity-peer-'.$i.'-'.$seed, true), 0, 20);
                $uploaded = $seeder ? 5_368_709_120 : 0;
                $downloaded = $seeder ? 1_073_741_824 : (int) ($i % 1_073_741_824);
                $left = $seeder ? 0 : 1_073_741_824;

                $rows[] = [
                    'peer_id'     => $peerIdRaw,
                    'ip'          => inet_pton(long2ip(ip2long('10.60.0.0') + ($i % 65000))),
                    'port'        => 10000 + ($i % 50000),
                    'agent'       => 'qBittorrent/4.6.0',
                    'uploaded'    => $uploaded,
                    'downloaded'  => $downloaded,
                    'left'        => $left,
                    'seeder'      => $seeder,
                    'active'      => true,
                    'visible'     => true,
                    'connectable' => true,
                    'torrent_id'  => $torrentId,
                    'user_id'     => $userId,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];

                // Every field the k6 swarm needs to announce AS this exact
                // already-existing peer (not invent a brand-new one): its
                // real passkey/info_hash and its real seeded baseline, so
                // the very first announce during a run computes a genuine,
                // non-zero credited delta against the true starting point
                // instead of a synthetic from-zero series.
                $peerManifestRows[] = [
                    'user_id'    => $userId,
                    'torrent_id' => $torrentId,
                    'passkey'    => $allPasskeys[$userIdx],
                    'info_hash'  => $allInfoHashesHex[$torrentIdx],
                    'peer_id'    => bin2hex($peerIdRaw),
                    'uploaded'   => $uploaded,
                    'downloaded' => $downloaded,
                    'left'       => $left,
                ];
            }

            DB::table('peers')->insert($rows);

            // `peer_announce_cursors` is the authoritative per-peer byte
            // baseline consulted by `ProcessAnnounce`; it is only backfilled
            // from `peers` once, at migration-apply time, so every peer
            // inserted afterwards (these fixtures) needs its own matching
            // cursor row, seeded to the epoch `received_at` so the very first
            // real announce for it is never mistaken for a stale/superseded
            // job (mirrors the migration's own backfill exactly).
            $cursorRows = array_map(static fn (array $peer): array => [
                'user_id'     => $peer['user_id'],
                'torrent_id'  => $peer['torrent_id'],
                'peer_id'     => $peer['peer_id'],
                'uploaded'    => $peer['uploaded'],
                'downloaded'  => $peer['downloaded'],
                'left'        => $peer['left'],
                'received_at' => self::EPOCH,
                'created_at'  => $peer['created_at'],
                'updated_at'  => $peer['updated_at'],
            ], $rows);
            DB::table('peer_announce_cursors')->insert($cursorRows);

            $bar->advance(\count($chunk));
        }
        $bar->finish();
        $this->newLine();

        // --- Manifest handed to k6: every seeded passkey/info_hash AND every
        // seeded peer record (passkey/info_hash/peer_id/seeded baseline), so
        // the swarm announces AS the real, already-existing fixture peers
        // with their true starting uploaded/downloaded/left -- not brand-new
        // synthetic peer identities layered on top of them -- plus the
        // canary identity. Deliberately excludes the canary's own
        // passkey/info_hash/peer record from the general arrays so the
        // swarm scenario can never pick it.
        $manifestPath = '/var/www/capacity-results/manifest.json';

        if (is_dir(\dirname($manifestPath))) {
            $manifest = [
                'canary'       => $canary,
                'web_password' => 'capacity-local-only',
                'usernames'    => DB::table('users')->where('username', 'like', 'capacity_user_%')->orderBy('id')->pluck('username')->all(),
                'passkeys'     => $allPasskeys,
                'info_hashes'  => $allInfoHashesHex,
                'peers'        => $peerManifestRows,
            ];
            file_put_contents($manifestPath, json_encode($manifest, \JSON_THROW_ON_ERROR));
            $this->components->info("Done. Manifest (passkeys/info_hashes/peers/canary) written to {$manifestPath} for k6.");
        } else {
            $this->components->info('Done. Canary peer for capacity:verify-canary: '.json_encode($canary));
        }

        return self::SUCCESS;
    }
}
