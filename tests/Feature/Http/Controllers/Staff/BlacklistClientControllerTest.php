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

use App\Http\Controllers\Staff\BlacklistClientController;
use App\Http\Requests\Staff\StoreBlacklistClientRequest;
use App\Http\Requests\Staff\UpdateBlacklistClientRequest;
use App\Models\BlacklistClient;
use App\Models\Group;
use App\Models\User;

beforeEach(function (): void {
    $this->staffUser = User::factory()->create([
        'group_id' => fn () => Group::factory()->create([
            'is_owner' => true,
            'is_admin' => true,
            'is_modo'  => true,
        ])->id,
    ]);
});

test('create returns an ok response', function (): void {
    $response = $this->actingAs($this->staffUser)->get(route('staff.blacklisted_clients.create'));
    $response->assertOk();
    $response->assertViewIs('Staff.blacklist.clients.create');
});

test('destroy returns an ok response', function (): void {
    $blacklistClient = BlacklistClient::factory()->create();

    $response = $this->actingAs($this->staffUser)->delete(route('staff.blacklisted_clients.destroy', [$blacklistClient]));
    $response->assertRedirect(route('staff.blacklisted_clients.index'));
    $response->assertSessionHas('success', 'Blacklisted client destroyed successfully!');

    $this->assertModelMissing($blacklistClient);
});

test('edit returns an ok response', function (): void {
    $blacklistClient = BlacklistClient::factory()->create();

    $response = $this->actingAs($this->staffUser)->get(route('staff.blacklisted_clients.edit', [$blacklistClient]));

    $response->assertOk();
    $response->assertViewIs('Staff.blacklist.clients.edit');
    $response->assertViewHas('client');
});

test('index returns an ok response', function (): void {
    $response = $this->actingAs($this->staffUser)->get(route('staff.blacklisted_clients.index'));
    $response->assertOk();
    $response->assertViewIs('Staff.blacklist.clients.index');
    $response->assertViewHas('clients');
});

test('store validates with a form request', function (): void {
    $this->assertActionUsesFormRequest(
        BlacklistClientController::class,
        'store',
        StoreBlacklistClientRequest::class
    );
});

test('store returns an ok response', function (): void {
    $response = $this->actingAs($this->staffUser)->post(route('staff.blacklisted_clients.store'), [
        'name'           => 'Test Name',
        'reason'         => 'Test Reason',
        'peer_id_prefix' => 'Test Peer ID Prefix',
    ]);
    $response->assertRedirect(route('staff.blacklisted_clients.index'));
    $response->assertSessionHas('success', 'Blacklisted client stored successfully!');
});

test('update validates with a form request', function (): void {
    $this->assertActionUsesFormRequest(
        BlacklistClientController::class,
        'update',
        UpdateBlacklistClientRequest::class
    );
});

test('update returns an ok response', function (): void {
    $blacklistClient = BlacklistClient::factory()->create();

    $response = $this->actingAs($this->staffUser)->patch(route('staff.blacklisted_clients.update', [$blacklistClient]), [
        'name'           => 'Test Name Updated',
        'reason'         => 'Test Reason Updated',
        'peer_id_prefix' => 'Test Peer ID Prefix Updated',
    ]);
    $response->assertRedirect(route('staff.blacklisted_clients.index'));
    $response->assertSessionHas('success', 'Blacklisted client was updated successfully!');
});
