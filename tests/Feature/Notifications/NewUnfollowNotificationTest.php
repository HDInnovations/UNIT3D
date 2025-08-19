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
use App\Notifications\NewUnfollow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

test('unfollow a user creates a notification for the followed user', function (): void {
    Notification::fake();

    $follower = User::factory()->create();
    $followed = User::factory()->create();

    $followed->followers()->attach($follower->id);

    $userNotificationSettings = UserNotification::factory()->create([
        'user_id'               => $followed->id,
        'block_notifications'   => 0,
        'show_account_unfollow' => 1,
    ]);

    // $follower unfollows $followed
    $response = $this->actingAs($follower)->delete(route('users.followers.destroy', ['user' => $followed]));

    $response->assertRedirectBack()
        ->assertSessionHas('success', \sprintf(trans('user.follow-revoked'), $followed->username));

    Notification::assertSentTo(
        [$followed],
        NewUnfollow::class
    );
    Notification::assertCount(1);
});

test('unfollow a user creates a notification for the followed user when following notifications are not disabled for specific group', function (): void {
    Notification::fake();

    $group = Group::factory()->create();
    $randomGroup = Group::factory()->create();

    $follower = User::factory()->create([
        'group_id' => $group->id,
    ]);
    $followed = User::factory()->create([
        'group_id' => $group->id,
    ]);

    $followed->followers()->attach($follower->id);

    $userNotificationSettings = UserNotification::factory()->create([
        'user_id'               => $followed->id,
        'block_notifications'   => 0,
        'show_account_unfollow' => 1,
        'json_account_groups'   => [$randomGroup->id],
    ]);

    // $follower unfollows $followed
    $response = $this->actingAs($follower)->delete(route('users.followers.destroy', ['user' => $followed]));

    $response->assertRedirectBack()
        ->assertSessionHas('success', \sprintf(trans('user.follow-revoked'), $followed->username));

    Notification::assertSentTo(
        [$followed],
        NewUnfollow::class
    );
    Notification::assertCount(1);
});

test('unfollow a user does not create a notification for the followed user when all notifications disabled', function (): void {
    Notification::fake();

    $group = Group::factory()->create();

    $follower = User::factory()->create([
        'group_id' => $group->id,
    ]);
    $followed = User::factory()->create([
        'group_id' => $group->id,
    ]);

    $followed->followers()->attach($follower->id);

    $userNotificationSettings = UserNotification::factory()->create([
        'user_id'               => $followed->id,
        'block_notifications'   => 1,
        'show_account_unfollow' => 1,
    ]);

    // $follower unfollows $followed
    $response = $this->actingAs($follower)->delete(route('users.followers.destroy', ['user' => $followed]));

    $response->assertRedirectBack()
        ->assertSessionHas('success', \sprintf(trans('user.follow-revoked'), $followed->username));

    Notification::assertCount(0);
});

test('unfollow a user does not create a notification for the followed user when following notifications are disabled', function (): void {
    Notification::fake();

    $group = Group::factory()->create();

    $follower = User::factory()->create([
        'group_id' => $group->id,
    ]);
    $followed = User::factory()->create([
        'group_id' => $group->id,
    ]);

    $followed->followers()->attach($follower->id);

    $userNotificationSettings = UserNotification::factory()->create([
        'user_id'               => $followed->id,
        'block_notifications'   => 0,
        'show_account_unfollow' => 0,
    ]);

    // $follower unfollows $followed
    $response = $this->actingAs($follower)->delete(route('users.followers.destroy', ['user' => $followed]));

    $response->assertRedirectBack()
        ->assertSessionHas('success', \sprintf(trans('user.follow-revoked'), $followed->username));

    Notification::assertCount(0);
});

test('unfollow a user does not create a notification for the followed user when following notifications are disabled for specific group', function (): void {
    Notification::fake();

    $group = Group::factory()->create();

    $follower = User::factory()->create([
        'group_id' => $group->id,
    ]);
    $followed = User::factory()->create([
        'group_id' => $group->id,
    ]);

    $followed->followers()->attach($follower->id);

    $userNotificationSettings = UserNotification::factory()->create([
        'user_id'               => $followed->id,
        'block_notifications'   => 0,
        'show_account_unfollow' => 1,
        'json_account_groups'   => [$group->id],
    ]);

    // $follower unfollows $followed
    $response = $this->actingAs($follower)->delete(route('users.followers.destroy', ['user' => $followed]));

    $response->assertRedirectBack()
        ->assertSessionHas('success', \sprintf(trans('user.follow-revoked'), $followed->username));

    Notification::assertCount(0);
});
