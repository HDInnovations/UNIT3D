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
use Override;

/**
 * @mixin \App\Models\User
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array{
     *     username: string,
     *     group: string,
     *     uploaded: int,
     *     downloaded: int,
     *     ratio: float,
     *     buffer: float,
     *     seeding?: int,
     *     leeching?: int,
     *     seedbonus: string,
     *     hit_and_runs: int,
     * }
     */
    #[Override]
    public function toArray(Request $request): array
    {
        return [
            'username'     => $this->username,
            'group'        => $this->group->name,
            'uploaded'     => $this->uploaded,
            'downloaded'   => $this->downloaded,
            'ratio'        => $this->ratio,
            'buffer'       => $this->buffer,
            'seeding'      => $this->whenCounted('seedingTorrents'),
            'leeching'     => $this->whenCounted('leechingTorrents'),
            'seedbonus'    => $this->seedbonus,
            'hit_and_runs' => $this->hitandruns,
        ];
    }
}
