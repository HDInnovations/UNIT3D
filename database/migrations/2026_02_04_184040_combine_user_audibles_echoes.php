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
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_audibles', function (Blueprint $table): void {
            $table->softDeletes()->after('status');
        });

        DB::table('user_audibles')
            ->leftJoin(
                'user_echoes',
                fn (JoinClause $join) => $join
                    ->on('user_audibles.user_id', '=', 'user_echoes.user_id')
                    ->on(
                        fn (JoinClause $join) => $join
                            ->on(fn (JoinClause $join) => $join->on('user_audibles.target_id', '=', 'user_echoes.target_id'))
                            ->orOn(fn (JoinClause $join) => $join->on('user_audibles.room_id', '=', 'user_echoes.room_id'))
                            ->orOn(fn (JoinClause $join) => $join->on('user_audibles.bot_id', '=', 'user_echoes.bot_id'))
                    )
            )
            ->whereNull('user_echoes.id')
            ->update([
                'deleted_at' => now(),
            ]);

        Schema::table('user_audibles', function (Blueprint $table): void {
            $table->renameColumn('status', 'audible');
            $table->rename('chat_conversations');
        });

        Schema::drop('user_echoes');
    }
};
