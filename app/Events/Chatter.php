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

namespace App\Events;

use App\Http\Resources\ChatMessageResource;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Queue\SerializesModels;

class Chatter implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public ?AnonymousResourceCollection $echoes = null;

    public ?ChatMessageResource $message = null;

    /**
     * @var null|array{
     *     type: 'bot'|'target',
     *     id: int
     * }
     */
    public ?array $ping = null;

    public ?AnonymousResourceCollection $audibles = null;

    /**
     * Chatter Constructor.
     */
    public function __construct(
        /** @var 'echo'|'audible'|'new.message'|'new.bot'|'new.ping' $type */
        public string $type,
        public int $target,
        /** @var (
         *      $type is 'echo'        ? AnonymousResourceCollection
         *   : ($type is 'audible'     ? AnonymousResourceCollection
         *   : ($type is 'new.message' ? ChatMessageResource
         *   : ($type is 'new.bot'     ? ChatMessageResource
         *   : ($type is 'new.ping'    ? array{type: 'bot'|'target', id: int}
         *   : never
         * ))))) $payload
         */
        mixed $payload,
    ) {
        if ($type == 'echo') {
            $this->echoes = $payload;
        } elseif ($type == 'audible') {
            $this->audibles = $payload;
        } elseif ($type == 'new.message') {
            $this->message = $payload;
        } elseif ($type == 'new.bot') {
            $this->message = $payload;
        } elseif ($type == 'new.ping') {
            $this->ping = $payload;
        }
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('chatter.'.$this->target);
    }
}
