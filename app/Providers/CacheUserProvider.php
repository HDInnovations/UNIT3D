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

namespace App\Providers;

use App\Helpers\CacheUser;
use App\Models\User;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Override;

class CacheUserProvider extends EloquentUserProvider
{
    public function __construct(HasherContract $hasher)
    {
        parent::__construct($hasher, User::class);
    }

    /**
     * @return User|\Illuminate\Contracts\Auth\Authenticatable|void|null
     */
    #[Override]
    public function retrieveById($identifier)
    {
        if (!$identifier || $identifier <= 0 || !is_numeric($identifier)) {
            return;
        }

        return CacheUser::user($identifier);
    }

    /**
     * @return User|\Illuminate\Contracts\Auth\Authenticatable|void|null
     */
    #[Override]
    public function retrieveByToken($identifier, $token)
    {
        if (!$identifier || $identifier <= 0 || !is_numeric($identifier)) {
            return;
        }

        $model = CacheUser::user($identifier);

        if (!$model) {
            return;
        }

        $rememberToken = $model->getRememberToken();

        return $rememberToken && hash_equals($rememberToken, $token) ? $model : null;
    }
}
