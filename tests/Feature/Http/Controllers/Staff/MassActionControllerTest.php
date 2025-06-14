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

use App\Models\Group;
use App\Models\User;
use Database\Seeders\GroupSeeder;

test('update returns an ok response', function (): void {
    $this->seed(GroupSeeder::class);
    User::factory()->times(3)->create([
        'email_verified_at' => null,
        'group_id'          => Group::firstWhere('slug', 'validating')
    ]);

    $this->get(route('staff.mass-actions.validate'))
        ->assertRedirect(route('staff.dashboard.index'));

    expect(User::where('email_verified_at', '=', null)->count())
        ->toBe(0, 'All users should be validated');
});
