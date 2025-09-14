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

use App\Helpers\Bbcode;
use hdvinnie\LaravelJoyPixels\LaravelJoyPixels;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Message
 */
class ChatMessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return array{
     *     id: int,
     *     bot: BotResource,
     *     user: ChatUserResource,
     *     receiver: ChatUserResource,
     *     chatroom: ChatRoomResource,
     *     message: string,
     *     created_at: string,
     *     updated_at: string,
     * }
     */
    public function toArray(Request $request): array
    {
        $emojiOne = new LaravelJoyPixels();

        $bbcode = new Bbcode();
        $logger = $bbcode->parse($this->message);
        $logger = $emojiOne->toImage($logger);

        if ($this->user_id == 1) {
            $logger = str_replace('a href="/#', 'a trigger="bot" class="chatTrigger" href="/#', $logger);
        }

        return [
            'id'         => $this->id,
            'bot'        => new BotResource($this->whenLoaded('bot')),
            'user'       => new ChatUserResource($this->whenLoaded('user')),
            'receiver'   => new ChatUserResource($this->whenLoaded('receiver')),
            'chatroom'   => new ChatRoomResource($this->whenLoaded('chatroom')),
            'message'    => $logger,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
