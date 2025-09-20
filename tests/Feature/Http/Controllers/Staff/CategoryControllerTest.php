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

use App\Http\Controllers\Staff\CategoryController;
use App\Http\Requests\Staff\StoreCategoryRequest;
use App\Http\Requests\Staff\UpdateCategoryRequest;
use App\Models\Category;
use App\Models\Group;
use App\Models\User;

beforeEach(function (): void {
    $this->staffUser = User::factory()->create([
        'group_id' => fn () => Group::factory()->create([
            'is_owner' => true,
            'is_admin' => true,
            'is_modo'  => true,
        ])->id,
    ]);
});

test('create returns an ok response', function (): void {
    $response = $this->actingAs($this->staffUser)->get(route('staff.categories.create'));
    $response->assertOk();
    $response->assertViewIs('Staff.category.create');
});

test('destroy returns an ok response', function (): void {
    $category = Category::factory()->create();

    $response = $this->actingAs($this->staffUser)->delete(route('staff.categories.destroy', [$category]));
    $response->assertRedirect(route('staff.categories.index'))->assertSessionHas('success', 'Category successfully deleted');

    $this->assertModelMissing($category);
});

test('edit returns an ok response', function (): void {
    $category = Category::factory()->create();

    $response = $this->actingAs($this->staffUser)->get(route('staff.categories.edit', [$category]));
    $response->assertOk();
    $response->assertViewIs('Staff.category.edit');
    $response->assertViewHas('category', $category);
});

test('index returns an ok response', function (): void {
    $response = $this->actingAs($this->staffUser)->get(route('staff.categories.index'));
    $response->assertOk();
    $response->assertViewIs('Staff.category.index');
    $response->assertViewHas('categories');
});

test('store validates with a form request', function (): void {
    $this->assertActionUsesFormRequest(
        CategoryController::class,
        'store',
        StoreCategoryRequest::class
    );
});

test('store returns an ok response', function (): void {
    $category = Category::factory()->make();
    $meta = ['movie', 'tv', 'game', 'music', 'no'];

    $response = $this->actingAs($this->staffUser)->post(route('staff.categories.store'), [
        'name'     => $category->name,
        'position' => $category->position,
        'image'    => $category->image,
        'icon'     => $category->icon,
        'meta'     => $meta[array_rand($meta)],
    ]);
    $response->assertRedirect(route('staff.categories.index'))->assertSessionHas('success', 'Category successfully added');
});

test('update validates with a form request', function (): void {
    $this->assertActionUsesFormRequest(
        CategoryController::class,
        'update',
        UpdateCategoryRequest::class
    );
});

test('update returns an ok response', function (): void {
    $category = Category::factory()->create();
    $meta = ['movie', 'tv', 'game', 'music', 'no'];

    $response = $this->actingAs($this->staffUser)->patch(route('staff.categories.update', [$category]), [
        'name'     => $category->name,
        'position' => $category->position,
        'image'    => $category->image,
        'icon'     => $category->icon,
        'meta'     => $meta[array_rand($meta)],
    ]);
    $response->assertRedirect(route('staff.categories.index'))->assertSessionHas('success', 'Category successfully modified');
});
