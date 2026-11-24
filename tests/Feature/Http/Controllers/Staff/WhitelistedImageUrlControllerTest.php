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

use App\Models\WhitelistedImageUrl;

test('destroy returns an ok response', function (): void {
    $whitelistedImageUrl = WhitelistedImageUrl::factory()->create();

    $this->delete(route('staff.whitelisted_image_urls.destroy', [$whitelistedImageUrl]))
        ->assertRedirectToRoute('staff.whitelisted_image_urls.index')
        ->assertSessionHas('success', 'Image url pattern removed from whitelist.');
    $this->assertModelMissing($whitelistedImageUrl);
});

test('index returns an ok response', function (): void {
    $this->get(route('staff.whitelisted_image_urls.index'))
        ->assertOk()
        ->assertViewIs('Staff.whitelisted-image-url.index');
});

test('store returns an ok response', function (): void {
    $whitelistedImageUrl = WhitelistedImageUrl::factory()->make();

    $this->post(route('staff.whitelisted_image_urls.store'), $whitelistedImageUrl->toArray())
        ->assertRedirectToRoute('staff.whitelisted_image_urls.index')
        ->assertSessionHas('success', 'New image url pattern whitelisted.');

    $this->assertDatabaseHas('whitelisted_image_urls', $whitelistedImageUrl->toArray());
});

test('update returns an ok response', function (): void {
    $whitelistedImageUrl = WhitelistedImageUrl::factory()->create();
    $newWhitelistedImageUrl = WhitelistedImageUrl::factory()->make();

    $this->patch(route('staff.whitelisted_image_urls.update', [$whitelistedImageUrl]), $newWhitelistedImageUrl->toArray())
        ->assertRedirectToRoute('staff.whitelisted_image_urls.index')
        ->assertSessionHas('success', 'Image url pattern updated successfully.');

    $this->assertDatabaseHas('whitelisted_image_urls', [
        'id' => $whitelistedImageUrl->id,
        ...$newWhitelistedImageUrl->toArray(),
    ]);
});
