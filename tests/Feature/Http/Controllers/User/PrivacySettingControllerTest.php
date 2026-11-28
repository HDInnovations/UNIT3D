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
use App\Models\UserPrivacy;

test('edit returns an ok response', function (): void {
    $user = User::factory()->create();
    $groups = Group::factory()->times(3)->create();

    $response = $this->actingAs($user)->get(route('users.privacy_settings.edit', [$user]))
        ->assertOk()
        ->assertViewIs('user.privacy-setting.edit')
        ->assertViewHas('user', $user)
        ->assertViewHas('groups');
});

test('update returns an ok response', function (): void {
    $user = User::factory()->create();
    $userPrivacy = UserPrivacy::factory()->for($user)->create();
    $newUserPrivacy = UserPrivacy::factory()->for($user)->make();

    $this->actingAs($user)
        ->patch(route('users.privacy_settings.update', [$user]), $newUserPrivacy->toArray())
        ->assertRedirectToRoute('users.privacy_settings.edit', [$user])
        ->assertSessionHas('success', 'Your privacy settings have been successfully saved.');

    $this->assertDatabaseHas('user_privacy', [
        'user_id'          => $user->id,
        'show_achievement' => $newUserPrivacy->show_achievement,
        'show_download'    => $newUserPrivacy->show_download,
        'show_follower'    => $newUserPrivacy->show_follower,
    ]);
});
