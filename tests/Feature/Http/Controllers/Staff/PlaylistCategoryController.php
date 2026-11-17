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

use App\Models\PlaylistCategory;

test('create returns an ok response', function (): void {
    $this->get(route('staff.playlist_categories.create'))
        ->assertOk()
        ->assertViewIs('Staff.playlist-category.create');
});

test('destroy returns an ok response', function (): void {
    $playlistCategory = PlaylistCategory::factory()->create();

    $this->delete(route('staff.playlist_categories.destroy', [$playlistCategory]))
        ->assertRedirectToRoute('staff.playlist_categories.index');
    $this->assertModelMissing($playlistCategory);
});

test('edit returns an ok response', function (): void {
    $playlistCategory = PlaylistCategory::factory()->create();

    $this->get(route('staff.playlist_categories.edit', [$playlistCategory]))
        ->assertOk()
        ->assertViewIs('Staff.playlist-category.edit')
        ->assertViewHas('playlistCategory', $playlistCategory);
});

test('index returns an ok response', function (): void {
    $playlistCategories = PlaylistCategory::factory()->times(3)->create();

    $this->get(route('staff.playlist_categories.index'))
        ->assertOk()
        ->assertViewIs('Staff.playlist-category.index');
});

test('store returns an ok response', function (): void {
    $playlistCategory = PlaylistCategory::factory()->make();

    $this->post(route('staff.playlist_categories.store'), $playlistCategory->toArray())
        ->assertRedirectToRoute('staff.playlist_categories.index');

    $this->assertDatabaseHas('playlist_categories', $playlistCategory->toArray());
});

test('update returns an ok response', function (): void {
    $playlistCategory = PlaylistCategory::factory()->create();
    $newPlaylistCategory = PlaylistCategory::factory()->make();

    $this->patch(route('staff.playlist_categories.update', [$playlistCategory]), $newPlaylistCategory->toArray())
        ->assertRedirectToRoute('staff.playlist_categories.index');

    $this->assertDatabaseHas('playlist_categories', [
        'id' => $playlistCategory->id,
        ...$newPlaylistCategory->toArray(),
    ]);
});
