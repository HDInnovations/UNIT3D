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
        Schema::create('user_privacy', function (Blueprint $table): void {
            $table->integer('id', true);
            $table->integer('user_id')->unique();
            $table->boolean('show_achievement')->index()->default(1);
            $table->boolean('show_bon')->index()->default(1);
            $table->boolean('show_comment')->index()->default(1);
            $table->boolean('show_download')->index()->default(0);
            $table->boolean('show_follower')->index()->default(1);
            $table->boolean('show_post')->index()->default(1);
            $table->boolean('show_profile')->index()->default(1);
            $table->boolean('show_profile_about')->index()->default(1);
            $table->boolean('show_profile_achievement')->index()->default(1);
            $table->boolean('show_profile_badge')->index()->default(1);
            $table->boolean('show_profile_follower')->index()->default(1);
            $table->boolean('show_profile_title')->index()->default(1);
            $table->boolean('show_profile_bon_extra')->index()->default(1);
            $table->boolean('show_profile_comment_extra')->index()->default(1);
            $table->boolean('show_profile_forum_extra')->index()->default(1);
            $table->boolean('show_profile_torrent_count')->index()->default(1);
            $table->boolean('show_profile_torrent_extra')->index()->default(1);
            $table->boolean('show_profile_torrent_ratio')->index()->default(1);
            $table->boolean('show_profile_torrent_seed')->index()->default(1);
            $table->boolean('show_profile_warning')->index()->default(1);
            $table->boolean('show_rank')->index()->default(1);
            $table->boolean('show_topic')->index()->default(1);
            $table->boolean('show_upload')->index()->default(0);
            $table->boolean('show_wishlist')->index()->default(1);
            $table->json('json_profile_groups');
            $table->json('json_torrent_groups');
            $table->json('json_forum_groups');
            $table->json('json_bon_groups');
            $table->json('json_comment_groups');
            $table->json('json_wishlist_groups');
            $table->json('json_follower_groups');
            $table->json('json_achievement_groups');
            $table->json('json_rank_groups');
            $table->foreign('user_id')->references('id')->on('users')->restrictOnUpdate()->cascadeOnDelete();
        });
    }
};
