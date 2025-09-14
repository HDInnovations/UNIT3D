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

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\UserEcho
 */
class UserEchoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array{
     *     id: int,
     *     user_id: int,
     *     user: ChatUserResource,
     *     target: ChatUserResource,
     *     room: \App\Models\Chatroom,
     *     bot: \App\Models\Bot,
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'id'      => $this->id,
            'user_id' => $this->user_id,
            'user'    => new ChatUserResource($this->user),
            'target'  => new ChatUserResource($this->target),
            'room'    => $this->room,
            'bot'     => $this->bot,
        ];
    }
}
