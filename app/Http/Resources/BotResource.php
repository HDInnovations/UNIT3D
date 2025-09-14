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
 * @mixin \App\Models\Bot
 */
class BotResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array{
     *     id: int,
     *     position: int,
     *     name: string,
     *     command: string,
     *     color: string|null,
     *     icon: string|null,
     *     emoji: string|null,
     *     help: string|null,
     *     active: bool,
     *     is_protected: bool,
     *     is_nerdbot: bool,
     *     is_systembot: bool,
     *     created_at: \Illuminate\Support\Carbon|null,
     *     updated_at: \Illuminate\Support\Carbon|null,
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'position'     => $this->position,
            'name'         => $this->name,
            'command'      => $this->command,
            'color'        => $this->color,
            'icon'         => $this->icon,
            'emoji'        => $this->emoji,
            'help'         => $this->help,
            'active'       => $this->active,
            'is_protected' => $this->is_protected,
            'is_nerdbot'   => $this->is_nerdbot,
            'is_systembot' => $this->is_systembot,
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
        ];
    }
}
