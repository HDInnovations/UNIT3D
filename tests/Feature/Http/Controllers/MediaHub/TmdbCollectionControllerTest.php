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

use App\Models\TmdbCollection;
use App\Models\User;

test('index returns an ok response', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('mediahub.collections.index'));
    $response->assertOk();
    $response->assertViewIs('mediahub.collection.index');
    $response->assertSeeLivewire('resources.views.livewire.app.http.livewire.tmdb.collection-search');
});

test('show returns an ok response', function (): void {
    $user = User::factory()->create();
    $collection = TmdbCollection::factory()->create();

    $response = $this->actingAs($user)->get(route('mediahub.collections.show', ['id' => $collection->id]));
    $response->assertOk();
    $response->assertViewIs('mediahub.collection.show');
    $response->assertViewHas('collection', $collection);
});
