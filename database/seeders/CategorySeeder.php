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

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::upsert([
            [
                'id'         => 1,
                'name'       => 'Movies',
                'position'   => 0,
                'icon'       => config('other.font-awesome').' fa-film',
                'image'      => null,
                'movie_meta' => 1,
                'tv_meta'    => 0,
                'game_meta'  => 0,
                'music_meta' => 0,
                'no_meta'    => 0,
            ],
            [
                'id'         => 2,
                'name'       => 'TV',
                'position'   => 1,
                'icon'       => config('other.font-awesome').' fa-tv-retro',
                'image'      => null,
                'movie_meta' => 0,
                'tv_meta'    => 1,
                'game_meta'  => 0,
                'music_meta' => 0,
                'no_meta'    => 0,
            ],
            [
                'id'         => 3,
                'name'       => 'Music',
                'position'   => 2,
                'icon'       => config('other.font-awesome').' fa-music',
                'image'      => null,
                'movie_meta' => 0,
                'tv_meta'    => 0,
                'game_meta'  => 0,
                'music_meta' => 1,
                'no_meta'    => 0,
            ],
            [
                'id'         => 4,
                'name'       => 'Games',
                'position'   => 3,
                'icon'       => config('other.font-awesome').' fa-gamepad',
                'image'      => null,
                'movie_meta' => 0,
                'tv_meta'    => 0,
                'game_meta'  => 1,
                'music_meta' => 0,
                'no_meta'    => 0,
            ],
            [
                'id'         => 5,
                'name'       => 'Software',
                'position'   => 4,
                'icon'       => config('other.font-awesome').' fa-laptop-code',
                'image'      => null,
                'movie_meta' => 0,
                'tv_meta'    => 0,
                'game_meta'  => 0,
                'music_meta' => 0,
                'no_meta'    => 1,
            ],
            [
                'id'         => 6,
                'name'       => 'Books',
                'position'   => 5,
                'icon'       => config('other.font-awesome').' fa-book',
                'image'      => null,
                'movie_meta' => 0,
                'tv_meta'    => 0,
                'game_meta'  => 0,
                'music_meta' => 0,
                'no_meta'    => 1,
            ],
            [
                'id'         => 7,
                'name'       => 'Other',
                'position'   => 6,
                'icon'       => config('other.font-awesome').' fa-box-open',
                'image'      => null,
                'movie_meta' => 0,
                'tv_meta'    => 0,
                'game_meta'  => 0,
                'music_meta' => 0,
                'no_meta'    => 1,
            ],
            [
                'id'         => 8,
                'name'       => 'XXX',
                'position'   => 7,
                'icon'       => config('other.font-awesome').' fa-triangle-exclamation',
                'image'      => null,
                'movie_meta' => 0,
                'tv_meta'    => 0,
                'game_meta'  => 0,
                'music_meta' => 0,
                'no_meta'    => 1,
            ],
        ], ['id'], ['name', 'position', 'icon', 'image', 'movie_meta', 'tv_meta', 'game_meta', 'music_meta', 'no_meta']);
    }
}
