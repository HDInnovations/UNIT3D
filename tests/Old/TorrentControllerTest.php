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

namespace Tests\Old;

use App\Enums\ModerationStatus;
use App\Models\Category;
use App\Models\Resolution;
use App\Models\Torrent;
use App\Models\Type;
use App\Models\User;
use Database\Seeders\BotSeeder;
use Database\Seeders\ChatroomSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\API\TorrentController
 */
final class TorrentControllerTest extends TestCase
{
    #[Test]
    public function filter_returns_an_ok_response(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')->getJson('api/torrents/filter');

        $response->assertOk()
            ->assertJson([
                'data'  => [],
                'links' => [
                    'first' => null,
                    'last'  => null,
                    'prev'  => null,
                    'next'  => null,
                    'self'  => \sprintf('%s/api/torrents', appurl()),
                ],
                'meta' => [
                    'path'        => \sprintf('%s/api/torrents/filter', appurl()),
                    'per_page'    => 25,
                    'next_cursor' => null,
                    'prev_cursor' => null,
                ],
            ]);
    }

    #[Test]
    public function index_returns_an_ok_response(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')->getJson(route('api.torrents.index'));

        $response->assertOk()
            ->assertJson([
                'data'  => [],
                'links' => [
                    'first' => \sprintf('%s/api/torrents?page=1', appurl()),
                    'last'  => \sprintf('%s/api/torrents?page=1', appurl()),
                    'prev'  => null,
                    'next'  => null,
                    'self'  => \sprintf('%s/api/torrents', appurl()),
                ],
                'meta' => [
                    'current_page' => 1,
                    'from'         => null,
                    'last_page'    => 1,
                    'path'         => \sprintf('%s/api/torrents', appurl()),
                    'per_page'     => 25,
                    'to'           => null,
                    'total'        => 0,
                ],
            ]);
    }

    #[Test]
    public function show_returns_an_ok_response(): void
    {
        $user = User::factory()->create();

        $torrent = Torrent::factory()->create([
            'user_id' => $user->id,
            'status'  => ModerationStatus::APPROVED,
        ]);

        $response = $this->actingAs($user, 'api')->getJson(\sprintf('api/torrents/%s', $torrent->id));

        $response->assertOk()
            ->assertJson([
                'type' => 'torrent',
                'id'   => $torrent->id,
            ]);
    }

    #[Test]
    public function store_returns_an_ok_response(): void
    {
        $this->seed(UserSeeder::class);
        $this->seed(ChatroomSeeder::class);
        $this->seed(BotSeeder::class);

        $user = User::factory()->create();

        $category = Category::factory()->create();
        $type = Type::factory()->create();
        $resolution = Resolution::factory()->create();

        $torrent = Torrent::factory()->make();

        $response = $this->actingAs($user, 'api')->postJson('api/torrents/upload', [
            'torrent' => new UploadedFile(
                base_path('tests/Resources/Pony Music - Mind Fragments (2014).torrent'),
                'Pony Music - Mind Fragments (2014).torrent'
            ),
            'category_id'   => $category->id,
            'name'          => 'Pony Music - Mind Fragments (2014)',
            'description'   => 'One song that represents the elements of being lost, abandoned, sadness and innocence.',
            'imdb'          => $torrent->imdb,
            'tvdb'          => $torrent->tvdb,
            'tmdb_movie_id' => $torrent->tmdb_movie_id,
            'tmdb_tv_id'    => $torrent->tmdb_tv_id,
            'mal'           => $torrent->mal,
            'igdb'          => $torrent->igdb,
            'type_id'       => $type->id,
            'resolution_id' => $resolution->id,
            'anonymous'     => $torrent->anon,
            'internal'      => $torrent->internal,
            'featured'      => false,
            'doubleup'      => $torrent->doubleup,
            'free'          => $torrent->free,
            'sticky'        => $torrent->sticky,
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Torrent uploaded successfully.',
            ]);
    }
}
