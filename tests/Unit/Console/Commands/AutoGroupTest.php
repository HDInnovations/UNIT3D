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

use App\Models\Group;
use App\Models\History;
use App\Models\Torrent;
use App\Models\User;

/**
 * @see App\Console\Commands\AutoGroup
 */
it('uses the same deleted-history seedtime average as group requirements', function (): void {
    $currentGroup = Group::factory()->create([
        'autogroup'       => true,
        'position'        => 1_000_000,
        'min_avg_seedtime' => null,
    ]);
    $targetGroup = Group::factory()->create([
        'autogroup'       => true,
        'position'        => 1_000_001,
        'min_avg_seedtime' => 150,
    ]);
    $user = User::factory()->create([
        'group_id' => $currentGroup->id,
    ]);

    $includedTorrent = Torrent::factory()->create(['user_id' => $user->id]);
    $deletedTorrent = Torrent::factory()->create(['user_id' => $user->id]);

    History::factory()->create([
        'user_id'    => $user->id,
        'torrent_id' => $includedTorrent->id,
        'seedtime'   => 200,
    ]);
    $deletedHistory = History::factory()->create([
        'user_id'    => $user->id,
        'torrent_id' => $deletedTorrent->id,
        'seedtime'   => 0,
    ]);
    History::query()
        ->where('user_id', '=', $user->id)
        ->where('torrent_id', '=', $deletedHistory->torrent_id)
        ->update(['deleted_at' => now()]);

    $response = $this->actingAs($user)->get(route('groups_requirements'));

    $response->assertOk();
    expect((float) $response->viewData('user_avg_seedtime'))->toBe(100.0);

    $this->artisan('auto:group', ['user_ids' => [$user->id]])
        ->assertExitCode(0)
        ->run();

    expect($user->fresh()->group_id)->toBe($currentGroup->id);
    expect($user->fresh()->group_id)->not->toBe($targetGroup->id);
});
