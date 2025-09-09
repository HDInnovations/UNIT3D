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
        // Remove existing duplicates
        DB::table('torrent_reseeds AS t1')
            ->join(
                'torrent_reseeds AS t2',
                fn ($join) => $join->on('t1.id', '>', 't2.id')
                    ->whereColumn('t1.user_id', '=', 't2.user_id')
                    ->whereColumn('t1.torrent_id', '=', 't2.torrent_id')
            )
            ->delete();

        Schema::table('torrent_reseeds', function (Blueprint $table): void {
            $table->unique(['torrent_id', 'user_id']);
            $table->unsignedInteger('requests_count')->default(1)->change();
        });
    }
};
