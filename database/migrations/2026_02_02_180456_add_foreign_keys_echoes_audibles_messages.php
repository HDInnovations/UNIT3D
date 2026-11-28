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
        Schema::table('messages', function (Blueprint $table): void {
            $table->unsignedInteger('chatroom_id')->nullable()->change();
        });

        DB::table('messages')
            ->where('chatroom_id', '=', 0)
            ->update(['chatroom_id' => null]);

        DB::table('messages')
            ->whereNotIn('bot_id', DB::table('bots')->select('id'))
            ->orWhereNotIn('chatroom_id', DB::table('chatrooms')->select('id'))
            ->delete();

        Schema::table('messages', function (Blueprint $table): void {
            $table->foreign('bot_id')->references('id')->on('bots')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('chatroom_id')->references('id')->on('chatrooms')->cascadeOnUpdate()->cascadeOnDelete();
        });

        DB::table('user_audibles')
            ->whereNotIn('bot_id', DB::table('bots')->select('id'))
            ->orWhereNotIn('room_id', DB::table('chatrooms')->select('id'))
            ->delete();

        Schema::table('user_audibles', function (Blueprint $table): void {
            $table->foreign('bot_id')->references('id')->on('bots')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('room_id')->references('id')->on('chatrooms')->cascadeOnUpdate()->cascadeOnDelete();
        });

        DB::table('user_echoes')
            ->whereNotIn('bot_id', DB::table('bots')->select('id'))
            ->orWhereNotIn('room_id', DB::table('chatrooms')->select('id'))
            ->delete();

        Schema::table('user_echoes', function (Blueprint $table): void {
            $table->foreign('bot_id')->references('id')->on('bots')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('room_id')->references('id')->on('chatrooms')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }
};
