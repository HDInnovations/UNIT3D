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
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('history', function (Blueprint $table): void {
            $table->dropIndex('history_user_id_foreign');
            $table->dropIndex(['immune']);
            $table->dropIndex(['hitrun']);
            $table->dropIndex(['user_id', 'torrent_id']);

            // Cheated torrent search
            $table->index(['completed_at']);
            $table->index(['created_at']);

            // User torrent history
            $table->index(['deleted_at', 'user_id', 'created_at']);

            // Fetching histories of a torrent
            $table->index(['torrent_id']);

            // Ghost peer clearing
            $table->index(['active', 'deleted_at', 'updated_at']);
        });
    }
};
