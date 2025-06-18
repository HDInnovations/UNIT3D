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
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_settings', function (Blueprint $table): void {
            $table->boolean('news_hidden')->default(false)->after('censor');
            $table->boolean('featured_hidden')->default(false)->after('chat_hidden');
            $table->boolean('random_media_hidden')->default(false)->after('featured_hidden');
            $table->boolean('poll_hidden')->default(false)->after('random_media_hidden');
            $table->boolean('top_torrents_hidden')->default(false)->after('poll_hidden');
            $table->boolean('top_users_hidden')->default(false)->after('top_torrents_hidden');
            $table->boolean('latest_topics_hidden')->default(false)->after('top_users_hidden');
            $table->boolean('latest_posts_hidden')->default(false)->after('latest_topics_hidden');
            $table->boolean('latest_comments_hidden')->default(false)->after('latest_posts_hidden');
            $table->boolean('online_hidden')->default(false)->after('latest_comments_hidden');
        });
    }
};
