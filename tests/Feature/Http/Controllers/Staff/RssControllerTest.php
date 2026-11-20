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
use App\Models\TmdbGenre;
use App\Models\Resolution;
use App\Models\Rss;
use App\Models\Type;

test('create returns an ok response', function (): void {
    $categories = Category::factory()->times(3)->create();
    $types = Type::factory()->times(3)->create();
    $resolutions = Resolution::factory()->times(3)->create();
    $genres = TmdbGenre::factory()->times(3)->create();

    $this->get(route('staff.rss.create'))
        ->assertOk()
        ->assertViewIs('Staff.rss.create')
        ->assertViewHas('categories')
        ->assertViewHas('types')
        ->assertViewHas('resolutions')
        ->assertViewHas('genres');
});

test('destroy returns an ok response', function (): void {
    $this->markTestIncomplete('assertModelMissing fails for some reason, have to debug why.');

    $rss = Rss::factory()->create(['is_private' => false]);

    $this->delete(route('staff.rss.destroy', [$rss]))
        ->assertRedirectToRoute('staff.rss.index')
        ->assertSessionHas('success', 'RSS Feed Deleted!');
    $this->assertModelMissing($rss);
});

test('destroy returns a 403 response', function (): void {
    $rss = Rss::factory()->create(['is_private' => true]);

    $this->delete(route('staff.rss.destroy', [$rss]))
        ->assertForbidden();
});

test('edit returns an ok response', function (): void {
    $rss = Rss::factory()->create(['is_private' => false]);
    $categories = Category::factory()->times(3)->create();
    $types = Type::factory()->times(3)->create();
    $resolutions = Resolution::factory()->times(3)->create();
    $genres = TmdbGenre::factory()->times(3)->create();

    $this->get(route('staff.rss.edit', [$rss]))
        ->assertOk()
        ->assertViewIs('Staff.rss.edit')
        ->assertViewHas('categories')
        ->assertViewHas('types')
        ->assertViewHas('resolutions')
        ->assertViewHas('genres')
        ->assertViewHas('rss');
});

test('edit returns a 403 response', function (): void {
    $rss = Rss::factory()->create(['is_private' => true]);

    $this->get(route('staff.rss.edit', [$rss]))
        ->assertForbidden();
});

test('index returns an ok response', function (): void {
    $rsses = Rss::factory()->times(3)->create();

    $this->get(route('staff.rss.index'))
        ->assertOk()
        ->assertViewIs('Staff.rss.index')
        ->assertViewHas('public_rss');
});

test('store returns an ok response', function (): void {
    $rss = Rss::factory()->make(['is_private' => false])->makeHidden('user_id', 'json_torrent');

    $this->post(route('staff.rss.store'), $rss->toArray())
        ->assertRedirectToRoute('staff.rss.index')
        ->assertSessionHas('success', 'Public RSS feed created');

    $this->assertDatabaseHas('rss', $rss->toArray());
});

test('update returns an ok response', function (): void {
    $rss = Rss::factory()->create(['is_private' => false]);
    $newRss = Rss::factory()->make(['is_private' => false])->makeHidden('user_id', 'json_torrent');

    $this->patch(route('staff.rss.update', [$rss]), $newRss->toArray())
        ->assertRedirectToRoute('staff.rss.index')
        ->assertSessionHas('success', 'Public RSS feed updated');

    $this->assertDatabaseHas('rss', [
        'id' => $rss->id,
        ...$newRss->toArray(),
    ]);
});

test('update returns a 403 response', function (): void {
    $rss = Rss::factory()->create(['is_private' => true]);
    $newRss = Rss::factory()->make();

    $this->patch(route('staff.rss.update', [$rss]), $newRss->toArray())
        ->assertForbidden();
});
