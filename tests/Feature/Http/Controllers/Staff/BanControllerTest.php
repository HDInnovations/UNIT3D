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

use App\Http\Controllers\Staff\BanController;
use App\Http\Requests\Staff\StoreBanRequest;
use App\Models\User;
use Database\Seeders\GroupSeeder;
use Illuminate\Support\Facades\Auth;

test('index returns an ok response', function (): void {
    $response = $this->get(route('staff.bans.index'));
    $response->assertOk();
    $response->assertViewIs('Staff.ban.index');
    $response->assertViewHas('bans');
});

test('store validates with a form request', function (): void {
    $this->assertActionUsesFormRequest(
        BanController::class,
        'store',
        StoreBanRequest::class
    );
});

test('store returns an ok response', function (): void {
    $this->seed(GroupSeeder::class);

    $user = User::factory()->create();

    $response = $this->post(route('staff.bans.store'), [
        'owned_by'   => $user->id,
        'ban_reason' => 'Test Ban Reason',
    ]);
    $response->assertRedirect(route('users.show', $user));
    $response->assertSessionHas('success', 'User is now banned!');
});

test('store aborts with a 403', function (): void {
    $this->seed(GroupSeeder::class);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('staff.bans.store'), [
        'owned_by'   => Auth::id(),
        'ban_reason' => 'Test Ban Reason',
    ]);
    $response->assertForbidden();
});
