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

namespace App\Listeners;

use App\Models\User;
use Illuminate\Auth\Events\Login;

class LoginListener
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        // Update Login Timestamp
        if ($event->user instanceof User) {
            $event->user->last_login = now();
            $event->user->save();
        }
    }
}
