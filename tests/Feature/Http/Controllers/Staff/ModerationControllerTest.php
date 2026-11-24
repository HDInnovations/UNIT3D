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
use App\Models\Group;
use App\Models\Torrent;
use App\Models\User;
use Database\Seeders\UserSeeder;

test('index returns an ok response', function (): void {
    $torrents = Torrent::factory()->times(3)->create();
    $user = User::factory()->for(Group::factory()->state(['is_torrent_modo' => true]))->create();

    $this->actingAs($user)->get(route('staff.moderation.index'))
        ->assertOk()
        ->assertViewIs('Staff.moderation.index')
        ->assertViewHas('current')
        ->assertViewHas('pending')
        ->assertViewHas('postponed')
        ->assertViewHas('rejected');
});

test('update returns an ok response', function (): void {
    $this->seed(UserSeeder::class);
    $torrent = Torrent::factory()->create(['status' => ModerationStatus::PENDING]);
    $user = User::factory()->for(Group::factory()->state(['is_torrent_modo' => true]))->create();

    $response = $this->actingAs($user)->post(route('staff.moderation.update', ['id' => $torrent->id]), [
        'old_status' => ModerationStatus::PENDING->value,
        'status'     => ModerationStatus::APPROVED->value,
    ])
        ->assertRedirectToRoute('staff.moderation.index')
        ->assertSessionHas('success', 'Torrent approved');

    $this->assertDatabaseHas('torrents', [
        'id'     => $torrent->id,
        'status' => ModerationStatus::APPROVED,
    ]);
});
