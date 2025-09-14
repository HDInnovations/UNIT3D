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

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $output = new Symfony\Component\Console\Output\ConsoleOutput();

        $locations = [
            [
                'baseNames'    => DB::table('articles')->whereNotNull('image')->where('image', '!=', '')->pluck('image')->toArray(),
                'oldDirectory' => public_path('files/img'),
                'newDirectory' => storage_path('app/images/articles/images'),
            ],
            [
                'baseNames'    => DB::table('ticket_attachments')->where('file_name', '!=', '')->pluck('file_name')->toArray(),
                'oldDirectory' => public_path('files/attachments'),
                'newDirectory' => storage_path('app/files/attachments/files'),
            ],
            [
                // cspell:ignore profil
                'baseNames'    => DB::table('users')->whereNotNull('image')->where('image', '!=', '')->whereNotIn('image', ['profil.png', 'profile.png'])->pluck('image')->toArray(),
                'oldDirectory' => public_path('files/img'),
                'newDirectory' => storage_path('app/images/users/avatars'),
            ],
            [
                'baseNames'    => DB::table('users')->whereNotNull('icon')->where('icon', '!=', '')->pluck('icon')->toArray(),
                'oldDirectory' => public_path('files/img'),
                'newDirectory' => storage_path('app/images/users/icons'),
            ],
            [
                'baseNames'    => DB::table('categories')->whereNotNull('image')->where('image', '!=', '')->pluck('image')->toArray(),
                'oldDirectory' => public_path('files/img'),
                'newDirectory' => storage_path('app/images/categories/images'),
            ],
            [
                'baseNames'    => DB::table('playlists')->whereNotNull('cover_image')->where('cover_image', '!=', '')->pluck('cover_image')->toArray(),
                'oldDirectory' => public_path('files/img'),
                'newDirectory' => storage_path('app/images/playlists/images'),
            ],
            [
                'baseNames'    => DB::table('subtitles')->where('file_name', '!=', '')->pluck('file_name')->toArray(),
                'oldDirectory' => public_path('files/subtitles'),
                'newDirectory' => storage_path('app/files/subtitles/files'),
            ],
            [
                'baseNames'    => array_map(fn ($id) => "torrent-banner_{$id}.jpg", DB::table('torrents')->pluck('id')->toArray()),
                'oldDirectory' => public_path('files/img'),
                'newDirectory' => storage_path('app/images/torrents/banners'),
            ],
            [
                'baseNames'    => array_map(fn ($id) => "torrent-cover_{$id}.jpg", DB::table('torrents')->pluck('id')->toArray()),
                'oldDirectory' => public_path('files/img'),
                'newDirectory' => storage_path('app/images/torrents/covers'),
            ],
            [
                'baseNames'    => DB::table('torrents')->where('file_name', '!=', '')->pluck('file_name')->toArray(),
                'oldDirectory' => public_path('files/torrents'),
                'newDirectory' => storage_path('app/files/torrents/files'),
            ],
        ];

        foreach ($locations as $location) {
            if (!file_exists($location['newDirectory'])) {
                mkdir($location['newDirectory'], 0755, true);
            }

            foreach ($location['baseNames'] as $baseName) {
                $oldPath = "{$location['oldDirectory']}/{$baseName}";

                if (file_exists($oldPath)) {
                    rename($oldPath, "{$location['newDirectory']}/{$baseName}");
                }
            }
        }

        File::deleteDirectory(public_path('files'));
    }
};
