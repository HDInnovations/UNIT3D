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
use App\Models\Group;
use App\Models\ForumPermission;

test('create returns an ok response', function (): void {
    $forums = Forum::factory()->times(3)->create();
    $groups = Group::factory()->times(3)->create();

    $response = $this->get(route('staff.forums.create'))
        ->assertOk()
        ->assertViewIs('Staff.forum.create')
        ->assertViewHas('categories')
        ->assertViewHas('groups');
});

test('destroy returns an ok response', function (): void {
    $forum = Forum::factory()->create();

    $this->delete(route('staff.forums.destroy', [$forum]))
        ->assertRedirectToRoute('staff.forum_categories.index')
        ->assertSessionHas('success', 'Forum has been deleted successfully');

    $this->assertModelMissing($forum);
});

test('edit returns an ok response', function (): void {
    $forum = Forum::factory()->create();
    $groups = Group::factory()->times(3)->create();

    $response = $this->get(route('staff.forums.edit', [$forum]))
        ->assertOk()
        ->assertViewIs('Staff.forum.edit')
        ->assertViewHas('categories')
        ->assertViewHas('groups')
        ->assertViewHas('forum', $forum);
});

test('store returns an ok response', function (): void {
    $forum = Forum::factory()->make();
    $permission = ForumPermission::factory()->make();
    $groups = Group::factory()->times(3)->create();

    $this->post(route('staff.forums.store'), [
        'forum'       => $forum->toArray(),
        'permissions' => [$permission->toArray()],
    ])
        ->assertRedirectToRoute('staff.forum_categories.index')
        ->assertSessionHas('success', 'Forum has been created successfully');
});

test('update returns an ok response', function (): void {
    $forum = Forum::factory()->create();
    $permission = ForumPermission::factory()->make();
    $groups = Group::factory()->times(3)->create();

    $this->patch(route('staff.forums.update', [$forum]), [
        'forum'       => $forum->toArray(),
        'permissions' => [$permission->toArray()],
    ])
        ->assertRedirectToRoute('staff.forum_categories.index')
        ->assertSessionHas('success', 'Forum has been edited successfully');
});
