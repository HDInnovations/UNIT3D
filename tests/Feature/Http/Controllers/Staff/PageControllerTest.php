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

use App\Models\Page;

use function Pest\Laravel\assertDatabaseHas;

test('create returns an ok response', function (): void {
    $this->get(route('staff.pages.create'))
        ->assertOk()
        ->assertViewIs('Staff.page.create');
});

test('destroy returns an ok response', function (): void {
    $page = Page::factory()->create();

    $this->delete(route('staff.pages.destroy', ['page' => $page]))
        ->assertRedirect(route('staff.pages.index'));

    $this->assertModelMissing($page);
});

test('edit returns an ok response', function (): void {
    $page = Page::factory()->create();

    $this->get(route('staff.pages.edit', ['page' => $page]))
        ->assertOk()
        ->assertViewIs('Staff.page.edit')
        ->assertViewHas('page');
});

test('index returns an ok response', function (): void {
    $pages = Page::factory()->times(3)->create();

    $this->get(route('staff.pages.index'))
        ->assertOk()
        ->assertViewIs('Staff.page.index')
        ->assertViewHas('pages', $pages);
});

test('store returns an ok response', function (): void {
    $page = Page::factory()->make();

    $this->post(route('staff.pages.store'), [
        'name'    => $page->name,
        'content' => $page->content,
    ])
        ->assertRedirect(route('staff.pages.index'))
        ->assertSessionHasNoErrors();

    assertDatabaseHas('pages', [
        'name'    => $page->name,
        'content' => $page->content,
    ]);
});

test('update returns an ok response', function (): void {
    $page = Page::factory()->create();

    $name = fake()->name;
    $content = fake()->text;

    $this->patch(route('staff.pages.update', ['page' => $page]), [
        'name'    => $name,
        'content' => $content,
    ])
        ->assertRedirect(route('staff.pages.index'))
        ->assertSessionHasNoErrors();

    $page->refresh();

    expect($page->name)
        ->toBe($name)
        ->and($page->content)
        ->toBe($content);
});
