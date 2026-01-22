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

namespace App\Http\Controllers\MediaHub;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\TmdbCollection;
use App\Models\TmdbCompany;
use App\Models\TmdbGenre;
use App\Models\TmdbMovie;
use App\Models\TmdbNetwork;
use App\Models\TmdbPerson;
use App\Models\TmdbTv;

class HomeController extends Controller
{
    /**
     * Display Media Hubs.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('mediahub.index', [
            'tv'               => TmdbTv::query()->count(),
            'movies'           => TmdbMovie::query()->count(),
            'movieCategoryIds' => Category::query()->where('movie_meta', '=', 1)->pluck('id')->toArray(),
            'tvCategoryIds'    => Category::query()->where('tv_meta', '=', 1)->pluck('id')->toArray(),
            'collections'      => TmdbCollection::query()->count(),
            'persons'          => TmdbPerson::query()->whereNotNull('still')->count(),
            'genres'           => TmdbGenre::query()->count(),
            'networks'         => TmdbNetwork::query()->count(),
            'companies'        => TmdbCompany::query()->count(),
        ]);
    }
}
