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

namespace App\Http\Controllers\API;

use App\Http\Resources\UserResource;
use App\Models\User;

class UserController extends BaseController
{
    final public function show(): UserResource
    {
        $user = User::query()
            ->whereKey(auth()->id())
            ->withCount([
                'torrents',
                'seedingTorrents',
                'leechingTorrents',
                'history as downloaded_count' => fn ($query) => $query->withTrashed()->where('actual_downloaded', '>', 0),
            ])
            ->withSum(['history' => fn ($query) => $query->withTrashed()], 'actual_uploaded')
            ->withSum(['history' => fn ($query) => $query->withTrashed()], 'uploaded')
            ->withSum(['history' => fn ($query) => $query->withTrashed()], 'actual_downloaded')
            ->withSum(['history' => fn ($query) => $query->withTrashed()], 'downloaded')
            ->withSum('seedingTorrents', 'size')
            ->withSum('uploadBonTransactions', 'cost')
            ->withAvg(['history' => fn ($query) => $query->withTrashed()], 'seedtime')
            ->sole();

        UserResource::withoutWrapping();

        return new UserResource($user);
    }
}
