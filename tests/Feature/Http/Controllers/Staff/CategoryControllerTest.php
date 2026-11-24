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

use App\Models\Category;

test('create returns an ok response', function (): void {
    $this->get(route('staff.categories.create'))
        ->assertOk()
        ->assertViewIs('Staff.category.create');
});

test('destroy returns an ok response', function (): void {
    $category = Category::factory()->create();

    $this->delete(route('staff.categories.destroy', [$category]))
        ->assertRedirect(route('staff.categories.index'))->assertSessionHas('success', 'Category successfully deleted');

    $this->assertModelMissing($category);
});

test('edit returns an ok response', function (): void {
    $category = Category::factory()->create();

    $this->get(route('staff.categories.edit', [$category]))
        ->assertOk()
        ->assertViewIs('Staff.category.edit')
        ->assertViewHas('category', $category);
});

test('index returns an ok response', function (): void {
    $this->get(route('staff.categories.index'))
        ->assertOk()
        ->assertViewIs('Staff.category.index')
        ->assertViewHas('categories');
});

test('store returns an ok response', function (): void {
    $category = Category::factory()->make();
    $meta = ['movie', 'tv', 'game', 'music', 'no'];

    $this->post(route('staff.categories.store'), [
        'name'     => $category->name,
        'position' => $category->position,
        'image'    => $category->image,
        'icon'     => $category->icon,
        'meta'     => $meta[array_rand($meta)],
    ])
        ->assertRedirect(route('staff.categories.index'))->assertSessionHas('success', 'Category successfully added');
});

test('update returns an ok response', function (): void {
    $category = Category::factory()->create();
    $meta = ['movie', 'tv', 'game', 'music', 'no'];

    $this->patch(route('staff.categories.update', [$category]), [
        'name'     => $category->name,
        'position' => $category->position,
        'image'    => $category->image,
        'icon'     => $category->icon,
        'meta'     => $meta[array_rand($meta)],
    ])
        ->assertRedirect(route('staff.categories.index'))->assertSessionHas('success', 'Category successfully modified');
});
