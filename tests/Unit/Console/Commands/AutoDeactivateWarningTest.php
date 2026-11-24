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
use App\Models\Warning;
use App\Notifications\UserWarningExpired;
use Illuminate\Support\Facades\Notification;

/**
 * @see App\Console\Commands\AutoDeactivateWarning
 */
it('runs successfully', function (): void {
    $this->artisan('auto:deactivate_warning')
        ->assertExitCode(0)
        ->run();
});

it('deactivates expired warnings for everyone but only notifies active users', function (): void {
    Notification::fake();

    $staff = User::factory()->create(['group_id' => UserGroup::USER->value]);
    $activeUser = User::factory()->create(['group_id' => UserGroup::USER->value]);
    $bannedUser = User::factory()->create(['group_id' => UserGroup::BANNED->value]);
    $prunedUser = User::factory()->create(['group_id' => UserGroup::USER->value]);
    $prunedUser->delete();

    foreach ([$activeUser, $bannedUser, $prunedUser] as $user) {
        Warning::create([
            'user_id'    => $user->id,
            'warned_by'  => $staff->id,
            'reason'     => 'Expired warning',
            'expires_on' => now()->subDay(),
            'active'     => true,
        ]);
    }

    $this->artisan('auto:deactivate_warning')
        ->assertExitCode(0)
        ->run();

    expect(Warning::query()->where('active', '=', true)->count())->toBe(0);

    Notification::assertSentTo($activeUser, UserWarningExpired::class);
    Notification::assertNotSentTo($bannedUser, UserWarningExpired::class);
    Notification::assertNotSentTo($prunedUser, UserWarningExpired::class);
});
