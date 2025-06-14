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

use App\Models\User;
use Database\Seeders\GroupSeeder;

test('index returns an ok response', function (): void {
    $this->seed(GroupSeeder::class);

    $response = $this->get('/dashboard');

    $response->assertOk();
    $response->assertViewIs('Staff.dashboard.index');
    $response->assertViewHas('users');
    $response->assertViewHas('torrents');
    $response->assertViewHas('peers');
    $response->assertViewHas('certificate');
    $response->assertViewHas('uptime');
    $response->assertViewHas('ram');
    $response->assertViewHas('disk');
    $response->assertViewHas('avg');
    $response->assertViewHas('basic');
    $response->assertViewHas('file_permissions');
});

test('dashboard is not available to regular users', function (): void {
    $this->actingAs(User::factory()->create())
        ->get('/dashboard')
        ->assertForbidden();
});
