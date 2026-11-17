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

use App\Models\Ban;
use App\Models\User;

test('store returns an ok response', function (): void {
    $user = User::factory()->create();
    $unban = Ban::factory()->make();

    $this->post(route('staff.unbans.store'), $unban->toArray())
        ->assertRedirectToRoute('users.show', $unban->user)
        ->assertSessionHas('success', 'User is now relieved of his ban!');

    $this->assertDatabaseHas('bans', [
        'unban_reason' => $unban->unban_reason,
    ]);
});
