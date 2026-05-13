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

/**
 * @see App\Console\Commands\AutoGroup
 */

use App\Models\Group;
use App\Models\User;

it('runs successfully', function (): void {
    $this->artisan('auto:group')
        ->assertExitCode(0)
        ->run();
});

it('runs successfully with targeted users', function (): void {
    $this->artisan('auto:group', ['user_ids' => [1, 2, 3]])
        ->assertExitCode(0)
        ->run();
});

it('does not promote a targeted user into a 2fa required group without confirmed 2fa', function (): void {
    $currentGroup = Group::query()->where('slug', '=', 'user')->sole();
    $targetGroup = Group::factory()->create([
        'autogroup'    => true,
        'position'     => 999,
        'requires_2fa' => true,
    ]);
    $user = User::factory()->create([
        'group_id'                => $currentGroup->id,
        'two_factor_confirmed_at' => null,
    ]);

    $this->artisan('auto:group', ['user_ids' => [$user->id]])
        ->assertExitCode(0)
        ->run();

    expect($user->refresh()->group_id)->toBe($currentGroup->id);
});

it('promotes a targeted user into a 2fa required group after confirmed 2fa', function (): void {
    $currentGroup = Group::query()->where('slug', '=', 'user')->sole();
    $targetGroup = Group::factory()->create([
        'autogroup'    => true,
        'position'     => 999,
        'requires_2fa' => true,
    ]);
    $user = User::factory()->create([
        'group_id'                => $currentGroup->id,
        'two_factor_confirmed_at' => now(),
    ]);

    $this->artisan('auto:group', ['user_ids' => [$user->id]])
        ->assertExitCode(0)
        ->run();

    expect($user->refresh()->group_id)->toBe($targetGroup->id);
});
