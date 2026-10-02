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
 */

use App\Enums\ModerationStatus;
use App\Models\Announce;
use App\Models\Torrent;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

function bufferAnnounce(int $userId, int $torrentId, string $jobUuid, string $peerId = 'p'): void
{
    Redis::connection('announce')->command('RPUSH', [
        config('cache.prefix').':announces:batch',
        serialize([
            'user_id'    => $userId,
            'torrent_id' => $torrentId,
            'uploaded'   => 1000,
            'downloaded' => 0,
            'left'       => 0,
            'corrupt'    => 0,
            'peer_id'    => str_pad($peerId, 20, '0'),
            'port'       => 6881,
            'numwant'    => 50,
            'event'      => '',
            'key'        => 'k',
            '_job_uuid'  => $jobUuid,
        ]),
    ]);
}

test('buffered announces are written to the database and removed from the batch', function (): void {
    Redis::connection('announce')->flushdb();
    DB::table('announce_job_receipts')->truncate();

    $user = User::factory()->create();
    $torrent = Torrent::factory()->create(['status' => ModerationStatus::APPROVED]);

    bufferAnnounce($user->id, $torrent->id, (string) Str::uuid());

    $this->artisan('auto:upsert_announces')->assertSuccessful();

    expect(Announce::query()->where('user_id', $user->id)->where('torrent_id', $torrent->id)->count())
        ->toBe(1)
        ->and(Redis::connection('announce')->command('LLEN', [config('cache.prefix').':announces:batch']))
        ->toBe(0);
});

test('a replayed duplicate job_uuid in the announces batch does not insert a duplicate log row', function (): void {
    Redis::connection('announce')->flushdb();
    DB::table('announce_job_receipts')->truncate();

    $user = User::factory()->create();
    $torrent = Torrent::factory()->create(['status' => ModerationStatus::APPROVED]);
    $jobUuid = (string) Str::uuid();

    bufferAnnounce($user->id, $torrent->id, $jobUuid);
    bufferAnnounce($user->id, $torrent->id, $jobUuid);

    $this->artisan('auto:upsert_announces')->assertSuccessful();

    expect(Announce::query()->where('user_id', $user->id)->where('torrent_id', $torrent->id)->count())->toBe(1);
});
