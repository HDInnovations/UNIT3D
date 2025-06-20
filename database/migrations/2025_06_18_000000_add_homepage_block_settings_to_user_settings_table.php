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
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_settings', function (Blueprint $table): void {
            // Rename chat_hidden to chat_visible and set default to true
            $table->renameColumn('chat_hidden', 'chat_visible');
            $table->boolean('chat_visible')->default(true)->change();

            $table->boolean('news_visible')->default(true)->after('censor');
            $table->boolean('featured_visible')->default(true)->after('chat_visible');
            $table->boolean('random_media_visible')->default(true)->after('featured_visible');
            $table->boolean('poll_visible')->default(true)->after('random_media_visible');
            $table->boolean('top_torrents_visible')->default(true)->after('poll_visible');
            $table->boolean('top_users_visible')->default(true)->after('top_torrents_visible');
            $table->boolean('latest_topics_visible')->default(true)->after('top_users_visible');
            $table->boolean('latest_posts_visible')->default(true)->after('latest_topics_visible');
            $table->boolean('latest_comments_visible')->default(true)->after('latest_posts_visible');
            $table->boolean('online_visible')->default(true)->after('latest_comments_visible');
        });

        DB::table('user_settings')->update([
            'chat_visible' => DB::raw('NOT chat_visible'),
        ]);
    }
};
