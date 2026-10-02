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

use App\Enums\ModerationStatus;
use App\Jobs\ProcessAnnounce;
use App\Models\Torrent;
use App\Models\User;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Redis;

test('index returns an ok response', function (): void {
    Redis::connection('announce')->flushdb();
    $user = User::factory()->create([
        'can_download' => true,
    ]);

    $info_hash = '16679042096019090177'; // 20 bytes
    $peer_id = '19045931013802080695'; // 20 bytes

    Torrent::factory()->create([
        'info_hash' => $info_hash,
        'status'    => ModerationStatus::APPROVED,
    ]);

    $headers = [
        'accept-language' => null,
        'referer'         => null,
        'accept-charset'  => null,
        'want-digest'     => null,
    ];

    $response = $this->withHeaders($headers)->get(route('announce', [
        'passkey'    => $user->passkey,
        'info_hash'  => $info_hash,
        'peer_id'    => $peer_id,
        'port'       => 7022,
        'left'       => 0,
        'uploaded'   => 1,
        'downloaded' => 1,
        'compact'    => 1,
    ]));
    $response ->assertOk();

    $this->assertStringNotContainsString('failure reason', $response->getContent());
});

test('the queued job is ordered by when the request arrived, not when it was dispatched', function (): void {
    Redis::connection('announce')->flushdb();
    Bus::fake();

    $user = User::factory()->create(['can_download' => true]);
    $infoHash = '16679042096019090177';
    Torrent::factory()->create(['info_hash' => $infoHash, 'status' => ModerationStatus::APPROVED]);

    $arrivedAt = now()->subSeconds(7);

    $this->withServerVariables([
        'REQUEST_TIME_FLOAT' => $arrivedAt->getTimestamp(),
        'HTTP_USER_AGENT'    => 'qBittorrent/5.2.4',
    ])
        ->withHeaders(['accept-language' => null, 'referer' => null, 'accept-charset' => null, 'want-digest' => null])
        ->get(route('announce', [
            'passkey'    => $user->passkey,
            'info_hash'  => $infoHash,
            'peer_id'    => '19045931013802080695',
            'port'       => 7022,
            'left'       => 0,
            'uploaded'   => 1,
            'downloaded' => 1,
            'compact'    => 1,
        ]))->assertOk()->assertDontSee('failure reason');

    Bus::assertDispatched(
        ProcessAnnounce::class,
        fn (ProcessAnnounce $job): bool => $job->receivedAt->getTimestamp() === $arrivedAt->getTimestamp(),
    );
});
