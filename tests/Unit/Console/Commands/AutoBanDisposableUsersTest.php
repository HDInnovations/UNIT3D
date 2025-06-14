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
 * @see App\Console\Commands\AutoBanDisposableUsers
 */

use Database\Seeders\GroupSeeder;

it('runs successfully', function (): void {
    $this->seed(GroupSeeder::class);

    $this->artisan('auto:ban_disposable_users')
        ->assertExitCode(0)
        ->run();

    // TODO: perform additional assertions to ensure the command behaved as expected
});
