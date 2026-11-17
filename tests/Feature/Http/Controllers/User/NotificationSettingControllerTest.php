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
use App\Models\User;
use App\Models\UserNotification;

test('edit returns an ok response', function (): void {
    $user = User::factory()->create();
    $groups = Group::factory()->times(3)->create();

    $response = $this->actingAs($user)->get(route('users.notification_settings.edit', [$user]))
        ->assertOk()
        ->assertViewIs('user.notification-setting.edit')
        ->assertViewHas('user', $user)
        ->assertViewHas('groups');
});

test('update returns an ok response', function (): void {
    $user = User::factory()->create();
    $userNotification = UserNotification::factory()->for($user)->create();
    $newUserNotification = UserNotification::factory()->for($user)->make();

    $this->actingAs($user)
        ->patch(route('users.notification_settings.update', [$user]), $newUserNotification->toArray())
        ->assertRedirectToRoute('users.notification_settings.edit', [$user])
        ->assertSessionHas('success', 'Your notification settings have been successfully saved.');

    $this->assertDatabaseHas('user_notifications', [
        'user_id'               => $user->id,
        'show_account_follow'   => $newUserNotification->show_account_follow,
        'show_account_unfollow' => $newUserNotification->show_account_unfollow,
        'show_following_upload' => $newUserNotification->show_following_upload,
    ]);
});
