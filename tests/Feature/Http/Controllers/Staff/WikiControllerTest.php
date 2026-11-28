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
 * @author     Roardom <roardom@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

use App\Models\Wiki;

test('create returns an ok response', function (): void {
    $this->get(route('staff.wikis.create'))
        ->assertOk()
        ->assertViewIs('Staff.wiki.create');
});

test('destroy returns an ok response', function (): void {
    $wiki = Wiki::factory()->create();

    $this->delete(route('staff.wikis.destroy', [$wiki]))
        ->assertRedirect(route('staff.wiki_categories.index'))
        ->assertSessionHas('success', 'Wiki has been deleted successfully');

    $this->assertModelMissing($wiki);
});

test('edit returns an ok response', function (): void {
    $wiki = Wiki::factory()->create();

    $this->get(route('staff.wikis.edit', [$wiki]))
        ->assertOk()
        ->assertViewIs('Staff.wiki.edit')
        ->assertViewHas('wiki', $wiki);
});

test('index returns an ok response', function (): void {
    Wiki::factory()->times(3)->create();

    $this->get(route('staff.wiki_categories.index'))
        ->assertOk()
        ->assertViewIs('Staff.wiki-category.index')
        ->assertViewHas('wikiCategories');
});

test('store returns an ok response', function (): void {
    $wiki = Wiki::factory()->make();

    $this->post(route('staff.wikis.store'), $wiki->toArray())
        ->assertRedirectToRoute('staff.wiki_categories.index')
        ->assertSessionHas('success', 'Wiki has been created successfully');

    $this->assertDatabaseHas('wikis', $wiki->toArray());
});

test('update returns an ok response', function (): void {
    $wiki = Wiki::factory()->create();
    $newWiki = Wiki::factory()->make();

    $this->patch(route('staff.wikis.update', [$wiki]), $newWiki->toArray())
        ->assertRedirectToRoute('staff.wiki_categories.index')
        ->assertSessionHas('success', 'Wiki has been edited successfully');

    $this->assertDatabaseHas('wikis', [
        'id' => $wiki->id,
        ...$newWiki->toArray(),
    ]);
});
