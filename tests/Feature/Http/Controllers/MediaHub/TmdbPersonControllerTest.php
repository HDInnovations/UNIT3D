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

use App\Models\TmdbPerson;
use App\Models\User;

test('index returns an ok response', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('mediahub.persons.index'));
    $response->assertOk();
    $response->assertViewIs('mediahub.person.index');
    $response->assertSeeLivewire('resources.views.livewire.app.http.livewire.tmdb.person-search');
});

test('show returns an ok response', function (): void {
    $user = User::factory()->create();
    $person = TmdbPerson::factory()->create();

    $response = $this->actingAs($user)->get(route('mediahub.persons.show', ['id' => $person->id]));
    $response->assertOk();
    $response->assertViewIs('mediahub.person.show');
    $response->assertViewHas('person', $person);
});
