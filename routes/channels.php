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
use Illuminate\Support\Facades\Broadcast;

/*
 * |--------------------------------------------------------------------------
 * | Broadcast Channels
 * |--------------------------------------------------------------------------
 * |
 * | Here you may register all of the event broadcasting channels that your
 * | application supports. The given channel authorization callbacks are
 * | used to check if an authenticated user can listen to the channel.
 * |
 */

Broadcast::channel('chatroom.{id}', fn ($user, $id) => User::query()
    ->select([
        'id',
        'username',
        'group_id',
        'image',
        'chatroom_id',
        'chat_status_id',
        'is_lifetime',
        'is_donor',
        'icon'
    ])
    ->with(['chatStatus:id,color', 'chatroom:id,name', 'group:id,color,effect,icon'])
    ->find($user->id));
Broadcast::channel('chatter.{id}', fn ($user, $id) => $user->id == $id);
