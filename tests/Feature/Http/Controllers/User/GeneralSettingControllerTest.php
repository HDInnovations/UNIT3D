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
use App\Models\UserSetting;

test('edit returns an ok response', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('users.general_settings.edit', [$user]))
        ->assertOk()
        ->assertViewIs('user.general-setting.edit')
        ->assertViewHas('user', $user);
});

test('update returns an ok response', function (): void {
    $user = User::factory()->create();
    $settings = UserSetting::factory()->for($user)->make();

    $this->actingAs($user)->patch(route('users.general_settings.update', [$user]), $settings->toArray())
        ->assertRedirectToRoute('users.general_settings.edit', [$user])
        ->assertSessionHas('success', 'Your general settings have been successfully saved.');

    $this->assertDatabaseHas('user_settings', [
        ...$settings->toArray()
    ]);
});
