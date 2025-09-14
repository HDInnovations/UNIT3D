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

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TorrentsResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array{
     *     data: \Illuminate\Http\Resources\Json\AnonymousResourceCollection,
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => TorrentResource::collection($this->collection),
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @return array{
     *     links: array{
     *         self: string,
     *     }
     * }
     */
    public function with(Request $request): array
    {
        return [
            'links' => [
                'self' => route('api.torrents.index'),
            ],
        ];
    }

    /**
     * Customize the outgoing response for the resource.
     */
    public function withResponse(Request $request, JsonResponse $response): void
    {
        $response->setEncodingOptions(JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
}
