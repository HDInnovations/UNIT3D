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
 * @author     Obi-wana
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

use App\Models\UploadContest;
use App\Models\Group;
use App\Models\User;

test('index upload contests returns an ok response', function (): void {
    $group = Group::factory()->create([
        'is_modo' => true,
    ]);
    $user = User::factory()->create([
        'group_id' => $group->id,
    ]);

    $uploadContests = UploadContest::factory()->times(3)->create();

    $response = $this->actingAs($user)->get(route('staff.upload_contests.index'));

    $response->assertOk();
    $response->assertViewIs('Staff.upload-contest.index');
    $response->assertViewHas('uploadContests');
});

test('store a new upload contest returns an ok response', function (): void {
    $group = Group::factory()->create([
        'is_modo' => true,
    ]);
    $user = User::factory()->create([
        'group_id' => $group->id,
    ]);

    $data = [
        'name'        => 'Test Upload Contest',
        'description' => 'This is a test upload contest.',
        'icon'        => 'fa-gamepad',
        'starts_at'   => now()->subDays(7)->format('Y-m-d'),
        'ends_at'     => now()->addDays(14)->format('Y-m-d'),
    ];

    $response = $this->actingAs($user)->post(route('staff.upload_contests.store'), $data);

    $response->assertRedirect(route('staff.upload_contests.index'));
    $this->assertDatabaseHas('upload_contests', $data);
});

test('update an upload contest returns an ok response', function (): void {
    $group = Group::factory()->create([
        'is_modo' => true,
    ]);
    $user = User::factory()->create([
        'group_id' => $group->id,
    ]);

    $uploadContest = UploadContest::factory()->create();

    $data = [
        'name'        => 'Updated Test Upload Contest',
        'description' => 'This is an updated test upload contest.',
        'icon'        => 'fa-gamepad',
        'active'      => 1,
        'starts_at'   => now()->subDays(7)->format('Y-m-d'),
        'ends_at'     => now()->addDays(14)->format('Y-m-d'),
    ];

    $response = $this->actingAs($user)->patch(route('staff.upload_contests.update', ['uploadContest' => $uploadContest]), $data);

    $response->assertRedirect(route('staff.upload_contests.index'));
    $this->assertDatabaseHas('upload_contests', $data);
});

test('destroy an upload contest returns an ok response', function (): void {
    $group = Group::factory()->create([
        'is_modo' => true,
    ]);
    $user = User::factory()->create([
        'group_id' => $group->id,
    ]);

    $uploadContest = UploadContest::factory()->create();

    $response = $this->actingAs($user)->delete(route('staff.upload_contests.destroy', $uploadContest));
    $response->assertRedirect(route('staff.upload_contests.index'));
    $this->assertDatabaseMissing('upload_contests', ['id' => $uploadContest->id]);
});
