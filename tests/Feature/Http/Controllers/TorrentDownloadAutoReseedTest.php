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
 */

declare(strict_types=1);

use App\Helpers\Bencode;
use App\Models\History;
use App\Models\Torrent;
use App\Models\TorrentReseed;
use App\Models\User;
use App\Notifications\NewReseedRequest;
use App\Repositories\ChatRepository;
use App\Http\Middleware\UpdateLastAction;
use Illuminate\Routing\Middleware\ThrottleRequestsWithRedis;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

beforeEach(function (): void {
    $this->withoutMiddleware(ThrottleRequestsWithRedis::class);
    $this->withoutMiddleware(UpdateLastAction::class);

    Storage::fake('torrent-files');
    Notification::fake();
});

function storeDownloadableTorrentFile(Torrent $torrent): void
{
    Storage::disk('torrent-files')->put($torrent->file_name, Bencode::bencode([
        'announce' => 'https://tracker.example/announce',
        'info'     => [
            'length'       => 1,
            'name'         => $torrent->name,
            'piece length' => 16_384,
            'pieces'       => str_repeat('a', 20),
        ],
    ]));
}

test('rss torrent download creates a reseed request for a dead torrent', function (): void {
    $requester = User::factory()->create([
        'can_download' => true,
        'downloaded'   => 1,
        'uploaded'     => 10_000,
    ]);
    $previousDownloader = User::factory()->create();
    $torrent = Torrent::factory()->create([
        'seeders' => 0,
    ]);
    History::factory()->create([
        'active'     => false,
        'torrent_id' => $torrent->id,
        'user_id'    => $previousDownloader->id,
    ]);
    storeDownloadableTorrentFile($torrent);

    $this->mock(ChatRepository::class, function ($mock): void {
        $mock->shouldReceive('systemMessage')->once();
    });

    $response = $this->get(route('torrent.download.rsskey', [
        'id'     => $torrent->id,
        'rsskey' => $requester->rsskey,
    ]));

    $response->assertOk();

    assertDatabaseHas('torrent_downloads', [
        'torrent_id' => $torrent->id,
        'user_id'    => $requester->id,
    ]);
    assertDatabaseHas('torrent_reseeds', [
        'requests_count' => 1,
        'torrent_id'     => $torrent->id,
        'user_id'        => $requester->id,
    ]);
    Notification::assertSentTo($previousDownloader, NewReseedRequest::class);
});

test('site torrent download does not create a reseed request for a healthy torrent', function (): void {
    $requester = User::factory()->create([
        'can_download' => true,
        'downloaded'   => 1,
        'uploaded'     => 10_000,
    ]);
    $torrent = Torrent::factory()->create([
        'seeders' => 3,
    ]);
    storeDownloadableTorrentFile($torrent);

    $this->mock(ChatRepository::class, function ($mock): void {
        $mock->shouldReceive('systemMessage')->never();
    });

    $response = $this->actingAs($requester)->get(route('download', ['id' => $torrent->id]));

    $response->assertOk();

    assertDatabaseHas('torrent_downloads', [
        'torrent_id' => $torrent->id,
        'user_id'    => $requester->id,
    ]);
    assertDatabaseMissing('torrent_reseeds', [
        'torrent_id' => $torrent->id,
    ]);
    expect(TorrentReseed::query()->where('torrent_id', '=', $torrent->id)->exists())->toBeFalse();
});
