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

describe('user model', function (): void {
    it('uses the configured show poster default when settings are missing', function (bool $showPoster): void {
        config(['other.default_show_poster' => $showPoster]);

        $user = User::factory()->create();

        expect($user->settings()->exists())->toBeFalse()
            ->and($user->settings->show_poster)->toBe($showPoster);
    })->with([
        'enabled'  => true,
        'disabled' => false,
    ]);
});
