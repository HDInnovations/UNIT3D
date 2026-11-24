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

use App\Models\User;

test('store returns an ok response', function (): void {
    $users = User::factory()->times(3)->create();

    $response = $this->post(route('staff.mass_email.store'), [
        'subject' => 'Test',
        'message' => 'test',
        'groups'  => [
            $users[0]->group_id,
            $users[2]->group_id,
        ],
    ]);

    $response->assertRedirect(route('staff.dashboard.index'));
});
