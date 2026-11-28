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
use App\Models\PersonalFreeleech;
use App\Models\User;
use App\Notifications\PersonalFreeleechDeleted;
use Illuminate\Support\Facades\Notification;

/**
 * @see App\Console\Commands\AutoRemovePersonalFreeleech
 */
it('runs successfully', function (): void {
    $this->artisan('auto:remove_personal_freeleech')
        ->assertExitCode(0)
        ->run();
});

it('removes expired personal freeleech for everyone but only notifies active users', function (): void {
    Notification::fake();

    $activeUser = User::factory()->create(['group_id' => UserGroup::USER->value]);
    $bannedUser = User::factory()->create(['group_id' => UserGroup::BANNED->value]);
    $prunedUser = User::factory()->create(['group_id' => UserGroup::USER->value]);
    $prunedUser->delete();

    foreach ([$activeUser, $bannedUser, $prunedUser] as $user) {
        PersonalFreeleech::factory()->create([
            'user_id'    => $user->id,
            'created_at' => now()->subDays(2),
        ]);
    }

    $this->artisan('auto:remove_personal_freeleech')
        ->assertExitCode(0)
        ->run();

    expect(PersonalFreeleech::query()->count())->toBe(0);

    Notification::assertSentTo($activeUser, PersonalFreeleechDeleted::class);
    Notification::assertNotSentTo($bannedUser, PersonalFreeleechDeleted::class);
    Notification::assertNotSentTo($prunedUser, PersonalFreeleechDeleted::class);
});
