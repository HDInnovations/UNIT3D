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
 * @mixin \App\Models\User
 */
class ChatUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array{
     *     id: int,
     *     username: string,
     *     chat_status: mixed,
     *     chat_status_id: int,
     *     chatroom_id: int,
     *     group: mixed,
     *     echoes: mixed,
     *     group_id: int,
     *     title: string,
     *     image: string,
     *     is_lifetime: bool,
     *     is_donor: bool,
     *     icon: string,
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'username'       => $this->username,
            'chat_status'    => $this->whenLoaded('chatStatus'),
            'chat_status_id' => $this->chat_status_id,
            'chatroom_id'    => $this->chatroom_id,
            'group'          => $this->whenLoaded('group'),
            'echoes'         => $this->whenLoaded('echoes'),
            'group_id'       => $this->group_id,
            'title'          => $this->title,
            'image'          => $this->image,
            'is_lifetime'    => $this->is_lifetime,
            'is_donor'       => $this->is_donor,
            'icon'           => $this->icon,
        ];
    }
}
