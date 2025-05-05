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
 * @author     Roardom <roardom@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE history DROP PRIMARY KEY, ADD PRIMARY KEY (torrent_id, user_id)');
        DB::statement('ALTER TABLE peers DROP PRIMARY KEY, ADD PRIMARY KEY (torrent_id, user_id, peer_id)');

        // Schema::table('history', function (Blueprint $table): void {
        //     $table->dropPrimary(['user_id', 'torrent_id']);
        //     $table->primary(['torrent_id', 'user_id']);
        // });

        // Schema::table('peers', function (Blueprint $table): void {
        //     $table->dropPrimary(['user_id', 'torrent_id', 'peer_id']);
        //     $table->primary(['torrent_id', 'user_id', 'peer_id']);
        // });
    }
};
