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

use App\Http\Controllers\Staff\PageController;
use App\Http\Requests\Staff\StorePageRequest;
use App\Http\Requests\Staff\UpdatePageRequest;
use App\Models\Page;
use Database\Seeders\GroupSeeder;

use function Pest\Laravel\assertDatabaseHas;

beforeEach(function (): void {
    $this->withoutVite();
    $this->withoutMiddleware(App\Http\Middleware\UpdateLastAction::class);
    $this->withoutMiddleware(Illuminate\Routing\Middleware\ThrottleRequestsWithRedis::class);
});

test('create returns an ok response', function (): void {
    $this->seed(GroupSeeder::class);

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
    Page::factory()->create(['footer_position' => 30]);
    Page::factory()->create(['footer_position' => 10]);
    Page::factory()->create(['footer_position' => 20]);

    $pages = Page::query()
        ->orderBy('footer_position')
        ->orderBy('id')
        ->get();

    $this->get(route('staff.pages.index'))
        ->assertOk()
        ->assertViewIs('Staff.page.index')
        ->assertViewHas('pages', $pages);
});

test('store validates with a form request', function (): void {
    $this->assertActionUsesFormRequest(
        PageController::class,
        'store',
        StorePageRequest::class
    );
});

test('store returns an ok response', function (): void {
    $page = Page::factory()->make();

    $this->post(route('staff.pages.store'), [
        'name'            => $page->name,
        'content'         => $page->content,
        'footer_position' => $page->footer_position,
    ])
        ->assertRedirect(route('staff.pages.index'))
        ->assertSessionHasNoErrors();

    assertDatabaseHas('pages', [
        'name'            => $page->name,
        'content'         => $page->content,
        'footer_position' => $page->footer_position,
    ]);
});

test('update validates with a form request', function (): void {
    $this->assertActionUsesFormRequest(
        PageController::class,
        'update',
        UpdatePageRequest::class
    );
});

test('update returns an ok response', function (): void {
    $page = Page::factory()->create();

    $name = fake()->name;
    $content = fake()->text;
    $footerPosition = 50;

    $this->patch(route('staff.pages.update', ['page' => $page]), [
        'name'            => $name,
        'content'         => $content,
        'footer_position' => $footerPosition,
    ])
        ->assertRedirect(route('staff.pages.index'))
        ->assertSessionHasNoErrors();

    $page->refresh();

    expect($page->name)
        ->toBe($name)
        ->and($page->content)
        ->toBe($content)
        ->and($page->footer_position)
        ->toBe($footerPosition);
});
