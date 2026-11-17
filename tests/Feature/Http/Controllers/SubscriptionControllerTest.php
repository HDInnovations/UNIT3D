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

use App\Models\Forum;
use App\Models\ForumPermission;
use App\Models\Subscription;
use App\Models\Topic;
use App\Models\User;

test('destroy returns an ok response', function (): void {
    $subscription = Subscription::factory()->create();

    $response = $this->actingAs($subscription->user)->post(route('subscriptions.destroy', [$subscription]))
        ->assertRedirectBack()
        ->assertSessionHas('success', 'You are now unsubscribed.');
});

test('index returns an ok response', function (): void {
    $user = User::factory()->create();
    Subscription::factory()->for($user)->count(3)->create();

    $this->actingAs($user)->get(route('subscriptions.index'))
        ->assertOk()
        ->assertViewIs('forum.subscriptions');
});

test('store forum returns an ok response', function (): void {
    $user = User::factory()->create();
    $forum = Forum::factory()->create();
    ForumPermission::factory()->create([
        'group_id'   => $user->group_id,
        'forum_id'   => $forum->id,
        'read_topic' => true,
    ]);

    $response = $this->actingAs($user)->post(route('subscriptions.store'), [
        'forum_id' => $forum->id,
    ])
        ->assertRedirectBack()
        ->assertSessionHasNoErrors();
});

test('store topic returns an ok response', function (): void {
    $user = User::factory()->create();
    $topic = Topic::factory()->create();
    ForumPermission::factory()->create([
        'group_id'   => $user->group_id,
        'forum_id'   => $topic->forum_id,
        'read_topic' => true,
    ]);

    $response = $this->actingAs($user)->post(route('subscriptions.store'), [
        'topic_id' => $topic->id,
    ])
        ->assertRedirectBack()
        ->assertSessionHasNoErrors();
});
