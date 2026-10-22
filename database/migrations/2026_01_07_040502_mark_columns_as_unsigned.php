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
        Schema::table('application_image_proofs', function (Blueprint $table): void {
            $table->unsignedInteger('application_id')->change();
        });

        Schema::table('application_url_proofs', function (Blueprint $table): void {
            $table->unsignedInteger('application_id')->change();
        });

        DB::table('automatic_torrent_freeleeches')->update([
            'freeleech_percentage' => DB::raw('GREATEST(0, freeleech_percentage)'),
        ]);

        Schema::table('automatic_torrent_freeleeches', function (Blueprint $table): void {
            $table->unsignedInteger('category_id')->nullable()->change();
            $table->unsignedInteger('type_id')->nullable()->change();
            $table->unsignedInteger('freeleech_percentage')->change();
        });

        Schema::table('bon_transactions', function (Blueprint $table): void {
            $table->increments('id')->change();
        });

        Schema::table('bots', function (Blueprint $table): void {
            $table->increments('id')->change();
        });

        Schema::table('forum_permissions', function (Blueprint $table): void {
            $table->increments('id')->change();
            $table->unsignedInteger('group_id')->change();
        });

        DB::table('forums')->update([
            'num_topic' => DB::raw('GREATEST(0, num_topic)'),
            'num_post'  => DB::raw('GREATEST(0, num_post)'),
        ]);

        Schema::table('forums', function (Blueprint $table): void {
            $table->unsignedInteger('num_topic')->nullable()->change();
            $table->unsignedInteger('num_post')->nullable()->change();
        });

        DB::table('groups')->update([
            'download_slots' => DB::raw('GREATEST(0, download_slots)'),
        ]);

        Schema::table('groups', function (Blueprint $table): void {
            $table->increments('id')->change();
            $table->unsignedInteger('download_slots')->nullable()->change();
        });

        Schema::table('likes', function (Blueprint $table): void {
            $table->unsignedInteger('post_id')->change();
        });

        DB::table('options')->update([
            'votes' => DB::raw('GREATEST(0, votes)'),
        ]);

        Schema::table('options', function (Blueprint $table): void {
            $table->unsignedInteger('votes')->default(0)->change();
        });

        Schema::table('pages', function (Blueprint $table): void {
            $table->increments('id')->change();
        });

        Schema::table('playlist_torrents', function (Blueprint $table): void {
            $table->unsignedInteger('playlist_id')->default(0)->change();
        });

        Schema::table('request_bounty', function (Blueprint $table): void {
            $table->increments('id')->change();
            $table->unsignedInteger('requests_id')->change();
        });

        Schema::table('request_claims', function (Blueprint $table): void {
            $table->unsignedInteger('request_id')->change();
        });

        Schema::table('requests', function (Blueprint $table): void {
            $table->increments('id')->change();
        });

        Schema::table('rss', function (Blueprint $table): void {
            $table->increments('id')->change();
        });

        DB::table('subtitles')->update([
            'downloads' => DB::raw('GREATEST(0, downloads)'),
        ]);

        Schema::table('subtitles', function (Blueprint $table): void {
            $table->unsignedInteger('language_id')->change();
            $table->unsignedInteger('downloads')->nullable()->change();
        });

        Schema::table('ticket_attachments', function (Blueprint $table): void {
            $table->unsignedInteger('ticket_id')->change();
        });

        Schema::table('tickets', function (Blueprint $table): void {
            $table->unsignedSmallInteger('category_id')->change();
            $table->unsignedSmallInteger('priority_id')->change();
        });

        DB::table('tmdb_movies')->update([
            'vote_count' => DB::raw('GREATEST(0, vote_count)'),
        ]);

        Schema::table('tmdb_movies', function (Blueprint $table): void {
            $table->unsignedInteger('vote_count')->nullable()->change();
        });

        DB::table('tmdb_tv')->update([
            'number_of_episodes'      => DB::raw('GREATEST(0, number_of_episodes)'),
            'count_existing_episodes' => DB::raw('GREATEST(0, count_existing_episodes)'),
            'count_total_episodes'    => DB::raw('GREATEST(0, count_total_episodes)'),
            'number_of_seasons'       => DB::raw('GREATEST(0, number_of_seasons)'),
            'vote_count'              => DB::raw('GREATEST(0, vote_count)'),
        ]);

        Schema::table('tmdb_tv', function (Blueprint $table): void {
            $table->unsignedInteger('number_of_episodes')->nullable()->change();
            $table->unsignedInteger('count_existing_episodes')->nullable()->change();
            $table->unsignedInteger('count_total_episodes')->nullable()->change();
            $table->unsignedInteger('number_of_seasons')->nullable()->change();
            $table->unsignedInteger('vote_count')->nullable()->change();
        });

        DB::table('topics')->update([
            'num_post' => DB::raw('GREATEST(0, num_post)'),
            'views'    => DB::raw('GREATEST(0, views)'),
        ]);

        Schema::table('topics', function (Blueprint $table): void {
            $table->unsignedInteger('num_post')->nullable()->change();
            $table->unsignedInteger('views')->nullable()->change();
        });

        DB::table('torrent_reseeds')->update([
            'requests_count' => DB::raw('GREATEST(0, requests_count)'),
        ]);

        Schema::table('torrent_reseeds', function (Blueprint $table): void {
            $table->unsignedInteger('requests_count')->default(0)->change();
        });

        DB::table('torrents')->update([
            'num_file'       => DB::raw('GREATEST(0, num_file)'),
            'season_number'  => DB::raw('GREATEST(0, season_number)'),
            'episode_number' => DB::raw('GREATEST(0, episode_number)'),
        ]);

        Schema::table('torrents', function (Blueprint $table): void {
            $table->unsignedInteger('num_file')->change();
            $table->unsignedInteger('season_number')->nullable()->change();
            $table->unsignedInteger('episode_number')->nullable()->change();
            $table->unsignedInteger('moderated_by')->nullable()->change();
            $table->unsignedInteger('distributor_id')->nullable()->change();
            $table->unsignedInteger('region_id')->nullable()->change();
        });

        Schema::table('user_audibles', function (Blueprint $table): void {
            $table->increments('id')->change();
            $table->unsignedInteger('room_id')->nullable()->change();
            $table->unsignedInteger('bot_id')->nullable()->change();
        });

        Schema::table('user_echoes', function (Blueprint $table): void {
            $table->increments('id')->change();
            $table->unsignedInteger('room_id')->nullable()->change();
            $table->unsignedInteger('bot_id')->nullable()->change();
        });

        Schema::table('user_notifications', function (Blueprint $table): void {
            $table->increments('id')->change();
        });

        Schema::table('user_privacy', function (Blueprint $table): void {
            $table->increments('id')->change();
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->unsignedInteger('group_id')->change();
        });

        // Posts

        Schema::table('forums', function (Blueprint $table): void {
            $table->dropForeign(['last_post_id']);
        });

        Schema::table('post_tips', function (Blueprint $table): void {
            $table->dropForeign(['post_id']);
        });

        Schema::table('topic_reads', function (Blueprint $table): void {
            $table->dropForeign(['last_read_post_id']);
        });
        Schema::table('topics', function (Blueprint $table): void {
            $table->dropForeign(['last_post_id']);
        });

        Schema::table('posts', function (Blueprint $table): void {
            $table->increments('id')->change();
        });

        Schema::table('forums', function (Blueprint $table): void {
            $table->unsignedInteger('last_post_id')->nullable()->change();

            $table->foreign('last_post_id')->references('id')->on('posts')->cascadeOnUpdate()->nullOnDelete();
        });

        Schema::table('post_tips', function (Blueprint $table): void {
            $table->unsignedInteger('post_id')->nullable()->change();

            $table->foreign('post_id')->references('id')->on('posts')->cascadeOnUpdate()->nullOnDelete();
        });
        Schema::table('topic_reads', function (Blueprint $table): void {
            $table->unsignedInteger('last_read_post_id')->change();

            $table->foreign('last_read_post_id')->references('id')->on('posts')->cascadeOnUpdate()->cascadeOnDelete();
        });
        Schema::table('topics', function (Blueprint $table): void {
            $table->unsignedInteger('last_post_id')->nullable()->change();

            $table->foreign('last_post_id')->references('id')->on('posts')->cascadeOnUpdate()->nullOnDelete();
        });
    }
};
