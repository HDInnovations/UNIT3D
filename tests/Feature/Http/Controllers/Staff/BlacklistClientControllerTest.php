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

use App\Models\BlacklistClient;

test('create returns an ok response', function (): void {
    $this->get(route('staff.blacklisted_clients.create'))
        ->assertOk()
        ->assertViewIs('Staff.blacklist.clients.create');
});

test('destroy returns an ok response', function (): void {
    $blacklistClient = BlacklistClient::factory()->create();

    $this->delete(route('staff.blacklisted_clients.destroy', [$blacklistClient]))
        ->assertRedirect(route('staff.blacklisted_clients.index'))
        ->assertSessionHas('success', 'Blacklisted client destroyed successfully!');

    $this->assertModelMissing($blacklistClient);
});

test('edit returns an ok response', function (): void {
    $blacklistClient = BlacklistClient::factory()->create();

    $this->get(route('staff.blacklisted_clients.edit', [$blacklistClient]))
        ->assertOk()
        ->assertViewIs('Staff.blacklist.clients.edit')
        ->assertViewHas('client');
});

test('index returns an ok response', function (): void {
    $this->get(route('staff.blacklisted_clients.index'))
        ->assertOk()
        ->assertViewIs('Staff.blacklist.clients.index')
        ->assertViewHas('clients');
});

test('store returns an ok response', function (): void {
    $this->post(route('staff.blacklisted_clients.store'), [
        'name'           => 'Test Name',
        'reason'         => 'Test Reason',
        'peer_id_prefix' => 'Test Peer ID Prefix',
    ])
        ->assertRedirect(route('staff.blacklisted_clients.index'))
        ->assertSessionHas('success', 'Blacklisted client stored successfully!');
});

test('update returns an ok response', function (): void {
    $blacklistClient = BlacklistClient::factory()->create();

    $this->patch(route('staff.blacklisted_clients.update', [$blacklistClient]), [
        'name'           => 'Test Name Updated',
        'reason'         => 'Test Reason Updated',
        'peer_id_prefix' => 'Test Peer ID Prefix Updated',
    ])
        ->assertRedirect(route('staff.blacklisted_clients.index'))
        ->assertSessionHas('success', 'Blacklisted client was updated successfully!');
});
