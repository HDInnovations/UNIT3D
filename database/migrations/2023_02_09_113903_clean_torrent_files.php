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

use App\Helpers\Bencode;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $directory = public_path().'/files/torrents/';

        DB::table('torrents')->select('file_name')->orderBy('id')->each(function ($torrent) use ($directory): void {
            if (file_exists($directory.$torrent->file_name)) {
                $dict = Bencode::bdecode_file($directory.$torrent->file_name);

                // Whitelisted keys
                $dict = array_intersect_key($dict, [
                    'announce'   => '',
                    'comment'    => '',
                    'created by' => '',
                    'encoding'   => '',
                    'info'       => '',
                ]);

                $dict['announce'] = config('app.url').'/announce/PID';

                $comment = config('torrent.comment', null);

                if ($comment !== null) {
                    $result['comment'] = $comment;
                }

                file_put_contents($directory.$torrent->file_name, Bencode::bencode($dict));
            }
        }, 100);
    }
};
