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
use App\Models\Torrent;
use App\Models\User;
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

test('announce rejects invalid numeric fields', function (string $field, string $value): void {
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

    $response = $this->withHeaders($headers)->get(route('announce', array_merge([
        'passkey'    => $user->passkey,
        'info_hash'  => $info_hash,
        'peer_id'    => $peer_id,
        'port'       => 7022,
        'left'       => 0,
        'uploaded'   => 1,
        'downloaded' => 1,
    ], [$field => $value])));

    $this->assertStringContainsString('Invalid '.$field.' !', $response->getContent());
})->with([
    'exabyte-scale uploaded'   => ['uploaded', '9000000000000000000'],
    'scientific notation'      => ['uploaded', '1e19'],
    'negative uploaded'        => ['uploaded', '-1'],
    'float uploaded'           => ['uploaded', '1.5'],
    'hex uploaded'             => ['uploaded', '0x10'],
    'above max left'           => ['left', '1125899906842625'], // MAX_ANNOUNCE_VALUE + 1
    'exabyte-scale downloaded' => ['downloaded', '99999999999999999999'],
    'float numwant'            => ['numwant', '25.5'],
    'negative corrupt'         => ['corrupt', '-1'],
]);

test('announce accepts legitimate boundary values', function (): void {
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

    // Exactly MAX_ANNOUNCE_VALUE (1 PiB), with numwant/corrupt omitted
    $response = $this->withHeaders($headers)->get(route('announce', [
        'passkey'    => $user->passkey,
        'info_hash'  => $info_hash,
        'peer_id'    => $peer_id,
        'port'       => 7022,
        'left'       => 1125899906842624,
        'uploaded'   => 1099511627776, // 1 TiB
        'downloaded' => 1,
    ]));
    $response->assertOk();

    $this->assertStringNotContainsString('failure reason', $response->getContent());
});
