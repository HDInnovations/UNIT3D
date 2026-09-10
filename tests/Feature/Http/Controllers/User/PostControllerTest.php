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
use App\Models\Post;
use App\Models\User;

test('index hides a user\'s anonymous posts from other users', function (): void {
    $author = User::factory()->create();
    $publicPost = Post::factory()->create([
        'anon'    => false,
        'user_id' => $author->id,
    ]);
    $anonymousPost = Post::factory()->create([
        'anon'    => true,
        'user_id' => $author->id,
    ]);
    $viewer = User::factory()->create();

    $response = $this->actingAs($viewer)->get(route('users.posts.index', [$author]));

    $response->assertOk();
    $response->assertViewIs('user.post.index');
    $response->assertViewHas('user', $author);

    $postIds = $response->viewData('posts')->modelKeys();

    expect($postIds)
        ->toContain($publicPost->id)
        ->not->toContain($anonymousPost->id);
});

test('index includes a user\'s anonymous posts for the author', function (): void {
    $author = User::factory()->create();
    $anonymousPost = Post::factory()->create([
        'anon'    => true,
        'user_id' => $author->id,
    ]);

    $response = $this->actingAs($author)->get(route('users.posts.index', [$author]));

    $response->assertOk();

    expect($response->viewData('posts')->modelKeys())->toContain($anonymousPost->id);
});

test('index includes a user\'s anonymous posts for moderators', function (): void {
    $author = User::factory()->create();
    $anonymousPost = Post::factory()->create([
        'anon'    => true,
        'user_id' => $author->id,
    ]);
    $moderator = User::factory()->create([
        'group_id' => Group::factory()->create(['is_modo' => true])->id,
    ]);

    $response = $this->actingAs($moderator)->get(route('users.posts.index', [$author]));

    $response->assertOk();

    expect($response->viewData('posts')->modelKeys())->toContain($anonymousPost->id);
});
