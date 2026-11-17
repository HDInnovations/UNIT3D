<?php

declare(strict_types=1);
use App\Models\ForumCategory;

/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D Community Edition is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D Community Edition
 *
 * @author     Roardom <roardom@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */
test('create returns an ok response', function (): void {
    $this
        ->get(route('staff.forum_categories.create'))
        ->assertOk()
        ->assertViewIs('Staff.forum-category.create');
});

test('destroy returns an ok response', function (): void {
    $forumCategory = ForumCategory::factory()->create();

    $this->delete(route('staff.forum_categories.destroy', [$forumCategory]))
        ->assertRedirect(route('staff.forum_categories.index'));

    $this->assertModelMissing($forumCategory);
});

test('edit returns an ok response', function (): void {
    $forumCategory = ForumCategory::factory()->create();

    $this->get(route('staff.forum_categories.edit', [$forumCategory]))
        ->assertOk()
        ->assertViewIs('Staff.forum-category.edit')
        ->assertViewHas('forumCategory', $forumCategory);
});

test('index returns an ok response', function (): void {
    $forumCategories = ForumCategory::factory()->times(3)->create();

    $this->get(route('staff.forum_categories.index'))
        ->assertOk()
        ->assertViewIs('Staff.forum-category.index')
        ->assertViewHas('categories');
});

test('store returns an ok response', function (): void {
    $forumCategory = ForumCategory::factory()->make();

    $this->post(route('staff.forum_categories.store'), $forumCategory->toArray())
        ->assertRedirectToRoute('staff.forum_categories.index');

    $this->assertDatabaseHas('forum_categories', $forumCategory->toArray());
});

test('update returns an ok response', function (): void {
    $forumCategory = ForumCategory::factory()->create();
    $newForumCategory = ForumCategory::factory()->make();

    $this->patch(route('staff.forum_categories.update', [$forumCategory]), $newForumCategory->toArray())
        ->assertRedirectToRoute('staff.forum_categories.index');

    $this->assertDatabaseHas('forum_categories', [
        'id' => $forumCategory->id,
        ...$newForumCategory->toArray(),
    ]);
});
