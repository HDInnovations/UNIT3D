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
use App\Models\History;
use App\Models\Torrent;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

function bufferHistory(int $userId, int $torrentId, string $jobUuid, int $uploaded): void
{
    Redis::connection('announce')->command('RPUSH', [
        config('cache.prefix').':histories:batch',
        serialize([
            'user_id'           => $userId,
            'torrent_id'        => $torrentId,
            'agent'             => 'qBittorrent/5.2.4',
            'uploaded'          => $uploaded,
            'actual_uploaded'   => $uploaded,
            'client_uploaded'   => $uploaded,
            'downloaded'        => 0,
            'actual_downloaded' => 0,
            'client_downloaded' => 0,
            'seeder'            => true,
            'active'            => true,
            'seedtime'          => 0,
            'immune'            => false,
            'completed_at'      => null,
            '_job_uuid'         => $jobUuid,
        ]),
    ]);
}

test('a replayed duplicate job_uuid in the histories batch is not applied twice', function (): void {
    Redis::connection('announce')->flushdb();

    $user = User::factory()->create();
    $torrent = Torrent::factory()->create(['status' => ModerationStatus::APPROVED]);
    $jobUuid = (string) Str::uuid();

    // A crash between this command's DB commit and its LTRIM (or a producer
    // redelivery) can legitimately put the exact same job_uuid's row into the
    // batch twice.
    bufferHistory($user->id, $torrent->id, $jobUuid, 1000);
    bufferHistory($user->id, $torrent->id, $jobUuid, 1000);

    $this->artisan('auto:upsert_histories')->assertSuccessful();

    expect((int) History::query()->where('user_id', $user->id)->where('torrent_id', $torrent->id)->value('uploaded'))
        ->toBe(1000)
        ->and(Redis::connection('announce')->command('LLEN', [config('cache.prefix').':histories:batch']))
        ->toBe(0);

    $receipt = DB::table('announce_job_receipts')
        ->where('job_uuid', $jobUuid)
        ->where('queue', 'histories')
        ->first();

    expect($receipt)->not->toBeNull()
        ->and($receipt->acknowledged_at)->not->toBeNull();
});

test('an unacknowledged receipt survives pruning no matter its age', function (): void {
    DB::table('announce_job_receipts')->insert([
        'job_uuid'        => (string) Str::uuid(),
        'queue'           => 'histories',
        'created_at'      => now()->subDays(2),
        'acknowledged_at' => null,
    ]);

    $this->artisan('auto:upsert_histories')->assertSuccessful();

    expect(DB::table('announce_job_receipts')->count())->toBe(1);
});

test('an acknowledged receipt older than the retention window is pruned once no pending outbox references it', function (): void {
    DB::table('announce_job_receipts')->insert([
        'job_uuid'        => (string) Str::uuid(),
        'queue'           => 'histories',
        'created_at'      => now()->subDays(2),
        'acknowledged_at' => now()->subDays(2),
    ]);

    $this->artisan('auto:upsert_histories')->assertSuccessful();

    expect(DB::table('announce_job_receipts')->count())->toBe(0);
});
