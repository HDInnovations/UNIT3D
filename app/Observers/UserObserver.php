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

namespace App\Observers;

use App\Models\User;
use Exception;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $_user): void
    {
        //\cache()->put(\sprintf('user:%s', $user->passkey), $user);
    }

    /**
     * Handle the User "saved" event.
     *
     *
     * @throws Exception
     */
    public function saved(User $user): void
    {
        //\cache()->put(\sprintf('user:%s', $user->passkey), $user);
        cache()->forget('cachedUser.'.$user->id);
    }

    /**
     * Handle the User "retrieved" event.
     */
    public function retrieved(User $_user): void
    {
        //\cache()->add(\sprintf('user:%s', $user->passkey), $user);
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $_user): void
    {
        //\cache()->forget(\sprintf('user:%s', $user->passkey));
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $_user): void
    {
        //\cache()->put(\sprintf('user:%s', $user->passkey), $user);
    }
}
