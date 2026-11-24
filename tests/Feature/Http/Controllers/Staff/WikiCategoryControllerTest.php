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

use App\Models\WikiCategory;

test('create returns an ok response', function (): void {
    $this->get(route('staff.wiki_categories.create'))
        ->assertOk()
        ->assertViewIs('Staff.wiki-category.create');
});

test('destroy returns an ok response', function (): void {
    $wikiCategory = WikiCategory::factory()->create();

    $this->delete(route('staff.wiki_categories.destroy', [$wikiCategory]))
        ->assertRedirect(route('staff.wiki_categories.index'))
        ->assertSessionHas('success', 'Wiki category successfully deleted');

    $this->assertModelMissing($wikiCategory);
});

test('edit returns an ok response', function (): void {
    $wikiCategory = WikiCategory::factory()->create();

    $this->get(route('staff.wiki_categories.edit', [$wikiCategory]))
        ->assertOk()
        ->assertViewIs('Staff.wiki-category.edit')
        ->assertViewHas('wikiCategory', $wikiCategory);
});

test('index returns an ok response', function (): void {
    WikiCategory::factory()->times(3)->create();

    $this->get(route('staff.wiki_categories.index'))
        ->assertOk()
        ->assertViewIs('Staff.wiki-category.index')
        ->assertViewHas('wikiCategories');
});

test('store returns an ok response', function (): void {
    $wikiCategory = WikiCategory::factory()->make();

    $this->post(route('staff.wiki_categories.store'), $wikiCategory->toArray())
        ->assertRedirect(route('staff.wiki_categories.index'))
        ->assertSessionHas('success', 'Wiki category successfully added');

    $this->assertDatabaseHas('wiki_categories', $wikiCategory->toArray());
});

test('update returns an ok response', function (): void {
    $wikiCategory = WikiCategory::factory()->create();
    $newWikiCategory = WikiCategory::factory()->make();

    $this->patch(route('staff.wiki_categories.update', [$wikiCategory]), $newWikiCategory->toArray())
        ->assertRedirect(route('staff.wiki_categories.index'))
        ->assertSessionHas('success', 'Wiki category successfully modified');

    $this->assertDatabaseHas('wiki_categories', [
        'id' => $wikiCategory->id,
        ...$newWikiCategory->toArray(),
    ]);
});
