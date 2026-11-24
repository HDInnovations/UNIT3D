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

use App\Models\MediaLanguage;

test('create returns an ok response', function (): void {
    $response = $this->get(route('staff.media_languages.create'))
        ->assertOk()
        ->assertViewIs('Staff.media-language.create');
});

test('destroy returns an ok response', function (): void {
    $mediaLanguage = MediaLanguage::factory()->create();

    $response = $this->delete(route('staff.media_languages.destroy', [$mediaLanguage]))
        ->assertRedirectToRoute('staff.media_languages.index')
        ->assertSessionHas('success', 'Media language has successfully been deleted');
    $this->assertModelMissing($mediaLanguage);
});

test('edit returns an ok response', function (): void {
    $mediaLanguage = MediaLanguage::factory()->create();

    $response = $this->get(route('staff.media_languages.edit', [$mediaLanguage]))
        ->assertOk()
        ->assertViewIs('Staff.media-language.edit')
        ->assertViewHas('media_language');
});

test('index returns an ok response', function (): void {
    $mediaLanguages = MediaLanguage::factory()->times(3)->create();

    $response = $this->get(route('staff.media_languages.index'))
        ->assertOk()
        ->assertViewIs('Staff.media-language.index')
        ->assertViewHas('media_languages');
});

test('store returns an ok response', function (): void {
    $mediaLanguage = MediaLanguage::factory()->make();
    $response = $this->post(route('staff.media_languages.store'), $mediaLanguage->toArray())
        ->assertRedirectToRoute('staff.media_languages.index')
        ->assertSessionHas('success', 'Media language successfully added');

    $this->assertDatabaseHas('media_languages', $mediaLanguage->toArray());
});

test('update returns an ok response', function (): void {
    $mediaLanguage = MediaLanguage::factory()->create();
    $newMediaLanguage = MediaLanguage::factory()->make();

    $response = $this->patch(route('staff.media_languages.update', [$mediaLanguage]), $newMediaLanguage->toArray())
        ->assertRedirectToRoute('staff.media_languages.index')
        ->assertSessionHas('success', 'Media language successfully updated');

    $this->assertDatabaseHas('media_languages', [
        'id' => $mediaLanguage->id,
        ...$newMediaLanguage->toArray(),
    ]);
});
