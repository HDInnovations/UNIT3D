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

namespace App\Http\Requests\API;

use App\Http\Requests\StoreTorrentRequestRequest as WebStoreTorrentRequestRequest;
use App\Models\Category;

class StoreTorrentRequestRequest extends WebStoreTorrentRequestRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $category = Category::query()->find($this->integer('category_id'));
        $tmdb = $this->input('tmdb') ?: null;

        $this->merge([
            'tmdb_movie_id' => $this->input('tmdb_movie_id') ?: ($category?->movie_meta ? $tmdb : null),
            'tmdb_tv_id'    => $this->input('tmdb_tv_id') ?: ($category?->tv_meta ? $tmdb : null),
            'imdb'          => $this->input('imdb') ?: null,
            'tvdb'          => $this->input('tvdb') ?: null,
            'mal'           => $this->input('mal') ?: null,
            'igdb'          => $this->input('igdb') ?: null,
            'anon'          => $this->input('anon', $this->input('anonymous', false)),
        ]);
    }
}
