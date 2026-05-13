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
use App\Notifications\UserPreWarning;

test('pre-warning notification mentions configured grace period', function (): void {
    config(['hitrun.grace' => 3]);

    $user = User::factory()->create();
    $notification = new UserPreWarning($user);

    expect(implode(' ', $notification->toMail($user)->introLines))
        ->toContain('The current hit and run grace period is 3 days from your last activity.');

    expect($notification->toArray($user)['body'])
        ->toContain('The current hit and run grace period is 3 days from your last activity.');
});

test('pre-warning notification uses singular day for a one day grace period', function (): void {
    config(['hitrun.grace' => 1]);

    $user = User::factory()->create();
    $notification = new UserPreWarning($user);

    expect(implode(' ', $notification->toMail($user)->introLines))
        ->toContain('The current hit and run grace period is 1 day from your last activity.');
});
