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

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTO\AnnounceGroupDTO;
use App\DTO\AnnounceQueryDTO;
use App\DTO\AnnounceTorrentDTO;
use App\DTO\AnnounceUserDTO;
use App\Enums\ModerationStatus;
use App\Exceptions\TrackerException;
use App\Jobs\ProcessAnnounce;
use App\Models\BlacklistClient;
use App\Models\Group;
use App\Models\Peer;
use App\Models\Scopes\ApprovedScope;
use App\Models\Torrent;
use App\Models\UnregisteredInfoHash;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Random\RandomException;
use Throwable;
use Exception;
use Illuminate\Support\Facades\Redis;

// cspell:ignore completei,downloadedi,incompletei,intervali

final class AnnounceController extends Controller
{
    // Announce Intervals
    private const int MIN = 1_800;
    private const int MAX = 3_600;

    // Port Blacklist
    private const array BLACK_PORTS = [
        // Hyper Text Transfer Protocol (HTTP) - port used for web traffic
        8080,
        8081,
        // Kazaa - peer-to-peer file sharing, some known vulnerabilities, and at least one worm (Benjamin) targeting it.
        1214,
        // IANA registered for Microsoft WBT Server, used for Windows Remote Desktop and Remote Assistance connections
        3389,
        // eDonkey 2000 P2P file sharing service. http://www.edonkey2000.com/
        4662,
        // Gnutella (FrostWire, Limewire, Shareaza, etc.), BearShare file sharing app
        6346,
        6347,
        // Port used by p2p software, such as WinMX, Napster.
        6699,
    ];

    private const array HEADERS = [
        'Content-Type'  => 'text/plain; charset=utf-8',
        'Cache-Control' => 'private, no-cache, no-store, must-revalidate, max-age=0',
        'Pragma'        => 'no-cache',
        'Expires'       => 0,
        'Connection'    => 'close'
    ];

    /**
     * Announce Code.
     *
     * @throws Exception
     * @throws Throwable
     */
    public function index(Request $request, string $passkey): ?Response
    {
        try {
            // Check client.
            $this->checkClient($request);

            // Check passkey.
            $this->checkPasskey($passkey);

            // Check and then get Announce queries.
            $queries = $this->checkAnnounceFields($request);

            // Check user via supplied passkey.
            $user = $this->checkUser($passkey, $queries);

            // Check users group.
            $group = $this->checkGroup($user);

            // Get Torrent Info Array from queries and judge if user can reach it.
            $torrent = $this->checkTorrent($queries->getInfoHash(), $user);

            // Check if a user is announcing a torrent as completed but no peer is in db.
            $this->checkPeer($torrent, $queries, $user);

            // Lock Min Announce Interval.
            $this->checkMinInterval($torrent, $queries, $user);

            // Check User Max Connections Per Torrent.
            $this->checkMaxConnections($torrent, $user);

            // Check Download Slots.
            if (($user->is_lifetime === false || $user->group->download_slots !== null) && config('announce.slots_system.enabled')) {
                $visible = $this->checkDownloadSlots($queries, $torrent, $user, $group);
            } else {
                $visible = true;
            }

            // Process Announce Job.
            $this->processAnnounceJob($queries, $user, $group, $torrent, $visible);

            if ($visible) {
                // Generate A Response For The Torrent Client.
                $response = $this->generateSuccessAnnounceResponse($queries, $torrent, $user);
            } else {
                $response = $this->generateWarningAnnounceResponse($torrent, new TrackerException(164, [':max' => $group->download_slots]));
            }
        } catch (TrackerException $exception) {
            $response = $this->generateFailedAnnounceResponse($exception);
        } catch (Exception) {
            // spell:disable-next-line
            $response = 'd14:failure reason21:Internal Server Error8:intervali5400e12:min intervali5400ee';
        }

        return $this->sendFinalAnnounceResponse($response);
    }

    /**
     * Check Client Is Valid.
     *
     * @throws TrackerException
     * @throws Throwable
     */
    private function checkClient(Request $request): void
    {
        // Query count check
        if ($request->query->count() < 6) {
            throw new TrackerException(129);
        }

        // Miss Header User-Agent is not allowed.
        if (!$request->header('User-Agent')) {
            throw new TrackerException(120);
        }

        // Block Other Browser, Crawler (May Cheater or Faker Client) by check Requests headers
        if (
            $request->header('accept-language')
            || $request->header('referer')
            || $request->header('accept-charset')

            /**
             * This header check may block Non-bittorrent client `Aria2` to access tracker,
             * Because they always add this header which other clients don't have.
             *
             * @see https://blog.rhilip.info/archives/1010/ ( in Chinese )
             */
            || $request->header('want-digest')
        ) {
            throw new TrackerException(122);
        }

        $userAgent = $request->header('User-Agent');

        // Should also block User-Agent strings that are too long. (For Database reasons)
        if (\strlen((string) $userAgent) > 64) {
            throw new TrackerException(123);
        }

        // Block Browser by checking the User-Agent
        if (preg_match('/(Mozilla|Browser|Chrome|Safari|AppleWebKit|Opera|Links|Lynx|Bot|Unknown)/i', (string) $userAgent)) {
            throw new TrackerException(121);
        }

        // Block Blacklisted Clients
        $blacklistedPeerIdPrefixes = cache()->rememberForever('client_blacklist', fn () => BlacklistClient::query()->pluck('peer_id_prefix')->toArray());

        $peerId = $request->query->getString('peer_id');

        foreach ($blacklistedPeerIdPrefixes as $blacklistedPeerIdPrefix) {
            if (str_starts_with($peerId, $blacklistedPeerIdPrefix)) {
                throw new TrackerException(128, [':ua' => $request->header('User-Agent')]);
            }
        }
    }

    /**
     * Check Passkey Exist and Valid.
     *
     * @throws TrackerException
     * @throws Throwable
     */
    private function checkPasskey(string $passkey): void
    {
        // If Passkey Is Not Provided Return Error to Client
        if ($passkey === '') {
            throw new TrackerException(130, [':attribute' => 'passkey']);
        }

        // If Passkey Length Is Wrong
        if (\strlen($passkey) !== 32) {
            throw new TrackerException(132, [':attribute' => 'passkey', ':rule' => 32]);
        }

        // If Passkey Format Is Wrong
        if (strspn(strtolower($passkey), 'abcdef0123456789') !== 32) {
            throw new TrackerException(131, [':attribute' => 'passkey', ':reason' => 'Passkey format is incorrect']);
        }
    }

    /**
     * Extract and validate Announce fields.
     *
     * @throws TrackerException
     * @throws Throwable
     */
    private function checkAnnounceFields(Request $request): AnnounceQueryDTO
    {
        $queries = [];

        // Part.1 Validate required announce fields
        foreach (['info_hash', 'peer_id', 'port', 'uploaded', 'downloaded', 'left'] as $item) {
            $itemData = $request->query->get($item);

            if (null !== $itemData) {
                $queries[$item] = $itemData;
            } else {
                throw new TrackerException(130, [':attribute' => $item]);
            }
        }

        foreach (['info_hash', 'peer_id'] as $item) {
            if (\strlen((string) $queries[$item]) !== 20) {
                throw new TrackerException(133, [':attribute' => $item, ':rule' => 20]);
            }
        }

        foreach (['port', 'uploaded', 'downloaded', 'left'] as $item) {
            $itemData = $queries[$item];

            if (!is_numeric($itemData) || $itemData < 0) {
                throw new TrackerException(134, [':attribute' => $item]);
            }
        }

        // Part.2 Extract optional announce fields
        foreach ([
            'event'   => '',
            'numwant' => 25,
            'corrupt' => 0,
            'key'     => '',
        ] as $item => $value) {
            $queries[$item] = $request->query->get($item, $value);
        }

        foreach (['numwant', 'corrupt'] as $item) {
            if (!is_numeric($queries[$item]) || $queries[$item] < 0) {
                throw new TrackerException(134, [':attribute' => $item]);
            }
        }

        $queries['event'] = strtolower((string) $queries['event']);

        if (!\in_array($queries['event'], ['started', 'completed', 'stopped', 'paused', ''])) {
            throw new TrackerException(136, [':event' => $queries['event']]);
        }

        // Part.3 check Port is Valid and Allowed
        if (
            !ctype_digit($queries['port'])
            // Block system-reserved ports since 99.9% of the time they're fake and thus not connectable
            // Some clients will send port of 0 on 'stopped' events. Let them through as they won't receive peers anyway.
            || ($queries['port'] < 1024 && $queries['event'] !== 'stopped')
            || $queries['port'] > 0xFFFF
            || \in_array($queries['port'], self::BLACK_PORTS, true)
        ) {
            throw new TrackerException(135, [':port' => $queries['port']]);
        }

        // Part.4 Get request ip and convert it to packed form
        $ip = $request->getClientIp();

        if ($ip === null) {
            throw new TrackerException(130, [':attribute' => 'ip']);
        }

        $ip = inet_pton($ip);

        if ($ip === false) {
            throw new TrackerException(130, [':attribute' => 'ip']);
        }

        return new AnnounceQueryDTO(
            (int) $queries['port'],
            (int) $queries['uploaded'],
            (int) $queries['downloaded'],
            (int) $queries['left'],
            (int) $queries['corrupt'],
            (int) $queries['numwant'],
            $queries['event'],
            (string) $queries['key'],
            $request->headers->get('user-agent') ?? '',
            (string) $queries['info_hash'],
            (string) $queries['peer_id'],
            $ip,
        );
    }

    /**
     * Get User Via Validated Passkey.
     *
     * @throws TrackerException
     * @throws Throwable
     */
    private function checkUser(string $passkey, AnnounceQueryDTO $queries): User
    {
        // Check Passkey Against Users Table
        $user = cache()->remember('user:'.$passkey, 8 * 3600, fn () => User::query()
            ->select(['id', 'group_id', 'can_download', 'is_donor', 'is_lifetime'])
            ->where('passkey', '=', $passkey)
            ->first());

        // If User Doesn't Exist Return Error to Client
        if ($user === null) {
            throw new TrackerException(140);
        }

        // If User Download Rights Are Disabled Return Error to Client
        if ($user->can_download === false && $queries->left !== 0) {
            throw new TrackerException(142);
        }

        return $user;
    }

    /**
     * Get Users Group.
     *
     * @throws TrackerException
     * @throws Throwable
     */
    private function checkGroup(User $user): Group
    {
        $deniedGroups = cache()->remember('denied_groups', 8 * 3600, fn () => DB::table('groups')
            ->selectRaw("min(case when slug = 'banned' then id end) as banned_id")
            ->selectRaw("min(case when slug = 'validating' then id end) as validating_id")
            ->selectRaw("min(case when slug = 'disabled' then id end) as disabled_id")
            ->first());

        // Get The Users Group
        $group = cache()->remember('group:'.$user->group_id, 8 * 3600, fn () => Group::query()
            ->select(['id', 'download_slots', 'is_immune', 'is_freeleech', 'is_double_upload'])
            ->find($user->group_id));

        // If User Account Is Unactivated/Validating Return Error to Client
        if ($user->group_id === $deniedGroups->validating_id) {
            throw new TrackerException(141, [':status' => 'Unactivated/Validating']);
        }

        // If User Is Banned Return Error to Client
        if ($user->group_id === $deniedGroups->banned_id) {
            throw new TrackerException(141, [':status' => 'Banned']);
        }

        // If User Is Disabled Return Error to Client
        if ($user->group_id === $deniedGroups->disabled_id) {
            throw new TrackerException(141, [':status' => 'Disabled']);
        }

        return $group;
    }

    /**
     * Check If Torrent Exist In Database.
     *
     * @throws TrackerException
     * @throws Throwable
     */
    private function checkTorrent(string $infoHash, User $user): Torrent
    {
        $torrent = cache()->remember(
            'announce-torrents:by-infohash:'.$infoHash,
            8 * 3600,
            fn () => Torrent::query()
                ->withoutGlobalScope(ApprovedScope::class)
                ->select(['id', 'free', 'doubleup', 'seeders', 'leechers', 'times_completed', 'status'])
                ->where('info_hash', '=', $infoHash)
                ->firstOr(fn (): string => '-1')
        );

        // If Torrent Doesn't Exist Return Error to Client
        if ($torrent === '-1') {
            UnregisteredInfoHash::query()->upsert([
                'user_id'   => $user->id,
                'info_hash' => $infoHash,
            ], ['info_hash', 'user_id']);

            throw new TrackerException(150);
        }

        // If Torrent Is Pending Moderation Return Error to Client
        if ($torrent->status === ModerationStatus::PENDING) {
            throw new TrackerException(151, [':status' => 'PENDING In Moderation']);
        }

        // If Torrent Is Rejected Return Error to Client
        if ($torrent->status === ModerationStatus::REJECTED) {
            throw new TrackerException(151, [':status' => 'REJECTED In Moderation']);
        }

        // If Torrent Is Postponed Return Error to Client
        if ($torrent->status === ModerationStatus::POSTPONED) {
            throw new TrackerException(151, [':status' => 'POSTPONED In Moderation']);
        }

        // Don't use eager loading so that we can make use of mysql prepared statement caching.
        // If we use eager loading, then laravel will use `where torrent_id in (123)` instead of `where torrent_id = ?`
        $torrent->setRelation(
            'peers',
            Peer::query()
                ->select(['torrent_id', 'peer_id', 'user_id', 'downloaded', 'uploaded', 'left', 'seeder', 'active', 'visible', 'ip', 'port', 'updated_at'])
                ->where('torrent_id', '=', $torrent->id)
                ->get()
        );

        return $torrent;
    }

    /**
     * Check If Peer Exist In Database.
     *
     * @throws TrackerException
     * @throws Throwable
     */
    private function checkPeer(Torrent $torrent, AnnounceQueryDTO $queries, User $user): void
    {
        if (
            $queries->event === 'completed'
            && $torrent->peers
                ->where('peer_id', '=', $queries->getPeerId())
                ->where('user_id', '=', $user->id)
                ->isEmpty()
        ) {
            throw new TrackerException(152);
        }
    }

    /**
     * Check A Peers Min Announce Interval.
     *
     * @throws TrackerException
     * @throws Exception
     * @throws Throwable
     */
    private function checkMinInterval(Torrent $torrent, AnnounceQueryDTO $queries, User $user): void
    {
        $event = match ($queries->event) {
            'started'   => 'started',
            'completed' => 'completed',
            'stopped'   => 'stopped',
            default     => 'empty',
        };

        $now = (int) now()->timestamp;

        // Detect broken (namely qBittorrent) clients sending duplicate announces
        // and eliminate them from screwing up stats.

        $duplicateAnnounceKey = config('cache.prefix').'announce-lock:'.$user->id.'-'.$torrent->id.'-'.$queries->getPeerId().'-'.$event;

        $lastAnnouncedAt = Redis::connection('announce')->command('SET', [$duplicateAnnounceKey, $now, ['NX', 'GET', 'EX' => 30]]);

        if ($lastAnnouncedAt !== false) {
            throw new TrackerException(162, [':elapsed' => $now - $lastAnnouncedAt]);
        }

        // Block clients disrespecting the min interval

        $lastAnnouncedKey = config('cache.prefix').'peer-last-announced:'.$user->id.'-'.$torrent->id.'-'.$queries->getPeerId();

        $randomMinInterval = random_int(intdiv(self::MIN * 85, 100), intdiv(self::MIN * 95, 100));

        $lastAnnouncedAt = Redis::connection('announce')->command('SET', [$lastAnnouncedKey, $now, ['NX', 'GET', 'EX' => $randomMinInterval]]);

        // Delete the timer if the user paused the torrent, and it's been at
        // least 5 minutes since they last announced.
        if ($event === 'stopped' && $lastAnnouncedAt < $now - 5 * 60) {
            Redis::connection('announce')->command('DEL', [$lastAnnouncedKey]);
        } elseif ($lastAnnouncedAt !== false && !\in_array($event, ['completed', 'stopped'])) {
            throw new TrackerException(162, [':elapsed' => $now - $lastAnnouncedAt]);
        }
    }

    /**
     * Check A Users Max Connections.
     *
     * @throws TrackerException
     * @throws Throwable
     */
    private function checkMaxConnections(Torrent $torrent, User $user): void
    {
        // Pull Count On Users Peers Per Torrent For Rate Limiting
        $connections = $torrent->peers
            ->where('user_id', '=', $user->id)
            ->where('active', '=', true)
            ->count();

        // If Users Peer Count On A Single Torrent Is Greater Than X Return Error to Client
        if ($connections > config('announce.rate_limit')) {
            throw new TrackerException(138, [':limit' => config('announce.rate_limit')]);
        }
    }

    /**
     * Check A Users Download Slots.
     *
     * @throws TrackerException
     * @throws Throwable
     */
    private function checkDownloadSlots(AnnounceQueryDTO $queries, Torrent $torrent, User $user, Group $group): bool
    {
        $max = $group->download_slots;

        $peer = $torrent->peers
            ->where('peer_id', '=', $queries->getPeerId())
            ->where('user_id', '=', $user->id)
            ->first();

        $cacheKey = 'user-leeching-count:'.$user->id;

        $count = cache()->get($cacheKey, 0);

        $isNewPeer = $peer === null || !$peer->visible;
        $isDeadPeer = $queries->event === 'stopped';
        $isSeeder = $queries->left === 0;

        $newLeech = $isNewPeer && !$isDeadPeer && !$isSeeder;
        $stoppedLeech = !$isNewPeer && $isDeadPeer && !$isSeeder;
        $leechBecomesSeed = !$isNewPeer && !$isDeadPeer && $isSeeder && $peer->left > 0;
        $seedBecomesLeech = !$isNewPeer && !$isDeadPeer && !$isSeeder && $peer->left === 0;

        if ($max !== null && $max >= 0 && ($newLeech || $seedBecomesLeech) && $count >= $max) {
            return false;
        }

        if ($newLeech || $seedBecomesLeech) {
            cache()->increment($cacheKey);
        } elseif ($stoppedLeech || $leechBecomesSeed) {
            cache()->decrement($cacheKey);
        }

        return true;
    }

    /**
     * Generate A Successful Announce Response For Client.
     *
     * @throws RandomException
     */
    private function generateSuccessAnnounceResponse(AnnounceQueryDTO $queries, Torrent $torrent, User $user): string
    {
        $peersIpv4 = '';
        $peersIpv6 = '';
        $peerCount = 0;
        $seederCount = 0;
        $leecherCount = 0;

        /**
         * For non `stopped` event only where either the torrent has at least one leech, or the user is a leech.
         * We query peers from database and send peerlist, otherwise just quick return.
         */
        if ($queries->event !== 'stopped') {
            $limit = (min($queries->numwant, 25));

            // Get Torrents Peers (Only include leechers in a seeder's peerlist)
            if ($queries->left === 0) {
                foreach ($torrent->peers as $peer) {
                    if ($peer->active && $peer->seeder) {
                        $seederCount++;
                    }

                    if ($peer->active && ! $peer->seeder) {
                        $leecherCount++;
                    }

                    // Don't include other seeders, inactive peers, invisible peers nor other peers belonging to the same user
                    if ($peer->seeder || !$peer->active || !$peer->visible || $peer->user_id === $user->id) {
                        continue;
                    }

                    switch (\strlen((string) $peer['ip'])) {
                        case 4:
                            $peersIpv4 .= $peer['ip'].pack('n', (int) $peer['port']);
                            $peerCount++;

                            break;
                        case 16:
                            $peersIpv6 .= $peer['ip'].pack('n', (int) $peer['port']);
                            $peerCount++;
                    }

                    if ($peerCount >= $limit) {
                        break;
                    }
                }
            } else {
                foreach ($torrent->peers as $peer) {
                    if ($peer->active && $peer->seeder) {
                        $seederCount++;
                    }

                    if ($peer->active && ! $peer->seeder) {
                        $leecherCount++;
                    }

                    // Don't include inactive peers, invisible peers, nor other peers belonging to the same user
                    if (!$peer->active || !$peer->visible || $peer->user_id === $user->id) {
                        continue;
                    }

                    switch (\strlen((string) $peer['ip'])) {
                        case 4:
                            $peersIpv4 .= $peer['ip'].pack('n', (int) $peer['port']);
                            $peerCount++;

                            break;
                        case 16:
                            $peersIpv6 .= $peer['ip'].pack('n', (int) $peer['port']);
                            $peerCount++;
                    }

                    if ($peerCount >= $limit) {
                        break;
                    }
                }
            }
        }

        // Build Response For Bittorrent Client
        // Keys must be ordered alphabetically
        $response = 'd8:completei'
            .$seederCount
            .'e10:downloadedi'
            .$torrent->times_completed
            .'e10:incompletei'
            .$leecherCount
            .'e8:intervali'
            .random_int(self::MIN, self::MAX)
            .'e12:min intervali'
            .random_int(intdiv(self::MIN * 95, 100), self::MIN)
            .'e';

        if ($peersIpv6 === '') {
            return $response.'5:peers'.\strlen($peersIpv4).':'.$peersIpv4.'e';
        }

        return $response.'5:peers'
            .\strlen($peersIpv4).':'.$peersIpv4
            .'6:peers6'
            .\strlen($peersIpv6).':'.$peersIpv6.'e';
    }

    /**
     * Generate A Warning Announce Response For Client.
     */
    private function generateWarningAnnounceResponse(Torrent $torrent, TrackerException $trackerException): string
    {
        $message = $trackerException->getMessage();

        return 'd8:completei'
            .$torrent->seeders
            .'e10:downloadedi'
            .$torrent->times_completed
            .'e10:incompletei'
            .$torrent->leechers
            .'e8:intervali'
            .random_int(self::MIN, self::MAX)
            .'e12:min intervali'
            .random_int(intdiv(self::MIN * 95, 100), self::MIN)
            .'e15:warning message'
            .\strlen($message)
            .':'
            .$message
            .'5:peers0:e';
    }

    /**
     * Process Announce Database Queries.
     */
    private function processAnnounceJob(AnnounceQueryDTO $queries, User $user, Group $group, Torrent $torrent, bool $visible): void
    {
        $groupDto = new AnnounceGroupDTO((bool) $group->is_freeleech, (bool) $group->is_double_upload, (bool) $group->is_immune);
        $userDto = new AnnounceUserDTO($user->id, $user->is_donor, $groupDto);
        $torrentDto = new AnnounceTorrentDTO($torrent->id, $torrent->free, $torrent->doubleup);

        ProcessAnnounce::dispatch($queries, $userDto, $torrentDto, $visible);
    }

    private function generateFailedAnnounceResponse(TrackerException $trackerException): string
    {
        $message = $trackerException->getMessage();

        if ($trackerException->getCode() === 151) {
            // If the torrent status is pending, postponed, or rejected, reduce the interval to 30 seconds.
            // This allows the uploader to start seeding sooner when the torrent is approved.
            return 'd14:failure reason'.\strlen($message).':'.$message.'8:intervali30e12:min intervali30ee';
        }

        return 'd14:failure reason'.\strlen($message).':'.$message.'8:intervali'.self::MIN.'e12:min intervali'.self::MIN.'ee';
    }

    /**
     * Send Final Announce Response.
     */
    private function sendFinalAnnounceResponse(string $response): Response
    {
        return response($response, headers: self::HEADERS);
    }
}
