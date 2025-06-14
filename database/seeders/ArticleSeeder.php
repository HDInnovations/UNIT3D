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

use App\Models\Article;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        Article::upsert([
            [
                'id'         => 1,
                'title'      => 'Welcome To '.config('other.title').' .',
                'content'    => 'Welcome to '.config('other.title').'. '.config('unit3d.powered-by').'.',
                'user_id'    => 3,
                'created_at' => '2017-02-28 17:22:37',
                'updated_at' => '2017-04-21 12:21:06',
            ],
        ], ['id'], ['updated_at' => DB::raw('updated_at')]);
    }
}
