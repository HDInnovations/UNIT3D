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

use App\Http\Middleware\BlockIpAddress;
use App\Http\Middleware\UpdateLastAction;
use App\Models\Bot;
use App\Models\Group;
use App\Models\Torrent;
use App\Models\User;
use App\Notifications\TorrentFeatured;
use Database\Seeders\UserSeeder;
use Illuminate\Routing\Middleware\ThrottleRequestsWithRedis;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;

test('granting featured status notifies the torrent uploader', function (): void {
    $this->withoutExceptionHandling();
    $this->withoutMiddleware([
        BlockIpAddress::class,
        ThrottleRequestsWithRedis::class,
        UpdateLastAction::class,
    ]);

    $this->seed(UserSeeder::class);

    Bot::factory()->create([
        'command' => 'systembot',
    ]);

    Event::fake();
    Notification::fake();

    $staff = User::factory()->create([
        'group_id' => Group::factory()->owner(),
    ]);

    $uploader = User::factory()->create();
    $torrent = Torrent::factory()->for($uploader)->create([
        'name' => 'Featured Release',
    ]);

    $response = $this->actingAs($staff)->post(route('torrent_feature', ['id' => $torrent->id]));

    $response->assertRedirect(route('torrents.show', ['id' => $torrent->id]));

    $this->assertDatabaseHas('featured_torrents', [
        'torrent_id' => $torrent->id,
        'user_id'    => $staff->id,
    ]);

    Notification::assertSentTo(
        $uploader,
        TorrentFeatured::class,
        fn (TorrentFeatured $notification): bool => $notification->torrent->is($torrent)
            && $notification->featuredBy->is($staff)
            && $notification->toArray($uploader) === [
                'title' => 'Your torrent has been featured',
                'body'  => $staff->username.' featured your uploaded torrent: Featured Release',
                'url'   => route('torrents.show', ['id' => $torrent->id], false),
            ],
    );
});
