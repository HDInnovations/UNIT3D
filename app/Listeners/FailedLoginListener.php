<?php
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

use App\Models\FailedLoginAttempt;
use App\Notifications\FailedLogin;

class FailedLoginListener
{
    /**
     * Handle the event.
     *
     * @param auth.failed $event
     *
     * @throws \Exception
     *
     * @return void
     */
    public function handle($event)
    {
        $bannedGroup = \cache()->rememberForever('banned_group', fn () => Role::where('slug', '=', 'banned')->pluck('id'));

        if (\property_exists($event, 'user') && $event->user !== null && $event->user instanceof \Illuminate\Database\Eloquent\Model
            && $event->user->role_id !== $bannedGroup[0]) {
            FailedLoginAttempt::record(
                $event->user,
                \request()->input('username'),
                \request()->ip()
            );

            $event->user->notify(new FailedLogin(
                \request()->ip()
            ));
        }
    }
}
