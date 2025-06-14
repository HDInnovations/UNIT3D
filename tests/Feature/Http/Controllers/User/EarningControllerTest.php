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

use App\Enums\UserGroup;
use App\Models\User;
use Database\Seeders\GroupSeeder;

test('index returns an ok response', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('users.earnings.index', [$user]));

    $response->assertOk();
    $response->assertViewIs('user.earning.index');
    $response->assertViewHas('user', $user);
});

test('index aborts with a 403', function (): void {
    $this->seed(GroupSeeder::class);

    $user = User::factory()->create([
        'group_id' => UserGroup::MODERATOR->value,
    ]);

    $authUser = User::factory()->create([
        'group_id' => UserGroup::USER->value,
    ]);

    $response = $this->actingAs($authUser)->get(route('users.earnings.index', [$user]));

    $response->assertForbidden();
});
