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
        // Missing still:
        //
        // For performance:
        //
        // - announces.{user_id,torrent_id}
        //
        // Foreign records can be deleted but we want to retain these records with the deleted id for transaction history:
        //
        // - bon_transactions.bon_exchange_id
        // - donations.package_id
        //
        // The tmdb ids are stored before the movie/tv records are fetched via http:
        //
        // - requests.{tmdb_movie_id,tmdb_tv_id}
        // - torrents.{tmdb_movie_id,tmdb_tv_id}
        //
        // Chatbox needs a separate refactor:
        //
        // - messages.{bot_id,chatroom_id}
        // - user_audibles.{room_id,bot_id}
        // - user_echoes.{room_id,bot_id}

        DB::table('application_image_proofs')
            ->whereNotIn('application_id', DB::table('applications')->select('id'))
            ->delete();

        Schema::table('application_image_proofs', function (Blueprint $table): void {
            $table->dropIndex(['application_id']);
            $table->foreign('application_id')->references('id')->on('applications')->cascadeOnUpdate()->cascadeOnDelete();
        });

        DB::table('application_url_proofs')
            ->whereNotIn('application_id', DB::table('applications')->select('id'))
            ->delete();

        Schema::table('application_url_proofs', function (Blueprint $table): void {
            $table->dropIndex(['application_id']);
            $table->foreign('application_id')->references('id')->on('applications')->cascadeOnUpdate()->cascadeOnDelete();
        });

        DB::table('automatic_torrent_freeleeches')
            ->whereNotIn('category_id', DB::table('categories')->select('id'))
            ->update([
                'category_id' => null,
            ]);

        DB::table('automatic_torrent_freeleeches')
            ->whereNotIn('resolution_id', DB::table('resolutions')->select('id'))
            ->update([
                'resolution_id' => null,
            ]);

        DB::table('automatic_torrent_freeleeches')
            ->whereNotIn('type_id', DB::table('types')->select('id'))
            ->update([
                'type_id' => null,
            ]);

        Schema::table('automatic_torrent_freeleeches', function (Blueprint $table): void {
            $table->unsignedSmallInteger('category_id')->nullable()->change();
            $table->unsignedSmallInteger('resolution_id')->nullable()->change();
            $table->unsignedSmallInteger('type_id')->nullable()->change();

            $table->foreign('category_id')->references('id')->on('categories')->cascadeOnUpdate()->nullOnDelete();
            $table->foreign('type_id')->references('id')->on('types')->cascadeOnUpdate()->nullOnDelete();
            $table->foreign('resolution_id')->references('id')->on('resolutions')->cascadeOnUpdate()->nullOnDelete();
        });

        Schema::table('donations', function (Blueprint $table): void {
            $table->dropIndex(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate();
            $table->dropIndex(['gifted_user_id']);
            $table->foreign('gifted_user_id')->references('id')->on('users')->cascadeOnUpdate();
        });

        DB::table('forum_permissions')
            ->whereNotIn('group_id', DB::table('groups')->select('id'))
            ->delete();

        Schema::table('forum_permissions', function (Blueprint $table): void {
            $table->dropIndex('fk_permissions_groups1_idx');
            $table->foreign('group_id')->references('id')->on('groups')->cascadeOnUpdate()->cascadeOnDelete();
        });

        DB::table('likes')
            ->whereNotIn('post_id', DB::table('posts')->select('id'))
            ->delete();

        Schema::table('likes', function (Blueprint $table): void {
            $table->foreign('post_id')->references('id')->on('posts')->cascadeOnUpdate()->cascadeOnDelete();
        });

        DB::table('playlist_torrents')
            ->whereNotIn('playlist_id', DB::table('playlists')->select('id'))
            ->delete();

        Schema::table('playlist_torrents', function (Blueprint $table): void {
            $table->dropIndex(['playlist_id']);
            $table->foreign('playlist_id')->references('id')->on('playlists')->cascadeOnUpdate()->cascadeOnDelete();
        });

        DB::table('reports')
            ->whereNotIn('reported_request_id', DB::table('requests')->select('id'))
            ->delete();

        DB::table('reports')
            ->whereNotIn('assigned_to', DB::table('users')->select('id'))
            ->update([
                'assigned_to' => 1,
            ]);

        Schema::table('reports', function (Blueprint $table): void {
            $table->dropIndex(['reported_request_id']);
            $table->foreign('reported_request_id')->references('id')->on('requests')->cascadeOnUpdate()->nullOnDelete();
            $table->foreign('assigned_to')->references('id')->on('users')->cascadeOnUpdate();
        });

        DB::table('request_bounty')
            ->whereNotIn('requests_id', DB::table('requests')->select('id'))
            ->delete();

        Schema::table('request_bounty', function (Blueprint $table): void {
            $table->dropIndex('request_id');
            $table->foreign('requests_id')->references('id')->on('requests')->cascadeOnUpdate()->cascadeOnDelete();
        });

        DB::table('request_claims')
            ->whereNotIn('request_id', DB::table('requests')->select('id'))
            ->delete();

        Schema::table('request_claims', function (Blueprint $table): void {
            $table->dropIndex('request_id');
            $table->foreign('request_id')->references('id')->on('requests')->cascadeOnUpdate()->cascadeOnDelete();
        });

        Schema::table('subtitles', function (Blueprint $table): void {
            $table->dropIndex(['language_id']);
            $table->foreign('language_id')->references('id')->on('media_languages')->cascadeOnUpdate();
        });

        foreach (DB::table('ticket_attachments')->whereNotIn('ticket_id', DB::table('tickets')->select('id'))->get() as $attachment) {
            $path = storage_path('app/files/attachments/files/'.$attachment->file_name);

            if (is_file($path)) {
                @unlink($path);
            }

            DB::table('ticket_attachments')->where('id', '=', $attachment->id)->delete();
        }

        Schema::table('ticket_attachments', function (Blueprint $table): void {
            $table->dropIndex(['ticket_id']);
            $table->foreign('ticket_id')->references('id')->on('tickets')->cascadeOnUpdate();
        });

        if (DB::table('tickets')->whereNotIn('category_id', DB::table('ticket_categories')->select('id'))->exists()) {
            $newTicketCategoryId = DB::table('ticket_categories')->insertGetId([
                'name'       => 'Other',
                'position'   => 999,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('tickets')
                ->whereNotIn('category_id', DB::table('ticket_categories')->select('id'))
                ->update([
                    'category_id' => $newTicketCategoryId,
                ]);
        }

        if (DB::table('tickets')->whereNotIn('priority_id', DB::table('ticket_priorities')->select('id'))->exists()) {
            $newTicketPriorityId = DB::table('ticket_priorities')->insertGetId([
                'name'       => 'Other',
                'position'   => 999,
                'color'      => '#000',
                'icon'       => 'fas fa-circle',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('tickets')
                ->whereNotIn('priority_id', DB::table('ticket_priorities')->select('id'))
                ->update([
                    'priority_id' => $newTicketPriorityId,
                ]);
        }

        Schema::table('tickets', function (Blueprint $table): void {
            $table->dropIndex(['category_id']);
            $table->foreign('category_id')->references('id')->on('ticket_categories')->cascadeOnUpdate();
            $table->dropIndex(['priority_id']);
            $table->foreign('priority_id')->references('id')->on('ticket_priorities')->cascadeOnUpdate();
        });

        DB::table('ticket_notes')
            ->whereNotIn('user_id', DB::table('users')->select('id'))
            ->update([
                'user_id' => 1,
            ]);

        DB::table('ticket_notes')
            ->whereNotIn('ticket_id', DB::table('tickets')->select('id'))
            ->delete();

        Schema::table('ticket_notes', function (Blueprint $table): void {
            $table->dropIndex(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate();
            $table->dropIndex(['ticket_id']);
            $table->foreign('ticket_id')->references('id')->on('tickets')->cascadeOnUpdate()->cascadeOnDelete();
        });

        DB::table('torrents')
            ->whereNotIn('distributor_id', DB::table('distributors')->select('id'))
            ->update([
                'distributor_id' => null,
            ]);

        DB::table('torrents')
            ->whereNotIn('region_id', DB::table('regions')->select('id'))
            ->update([
                'region_id' => null,
            ]);

        DB::table('torrents')
            ->whereNotIn('moderated_by', DB::table('users')->select('id'))
            ->update([
                'moderated_by' => 1,
            ]);

        Schema::table('torrents', function (Blueprint $table): void {
            $table->dropIndex(['distributor_id']);
            $table->foreign('distributor_id')->references('id')->on('distributors')->cascadeOnUpdate();
            $table->dropIndex(['region_id']);
            $table->foreign('region_id')->references('id')->on('regions')->cascadeOnUpdate();
            $table->dropIndex('moderated_by');
            $table->foreign('moderated_by')->references('id')->on('users');
        });

        DB::table('users')
            ->whereNotIn('group_id', DB::table('groups')->select('id'))
            ->update([
                'group_id' => DB::table('groups')->where('slug', '=', 'banned')->value('id'),
            ]);

        DB::table('users')
            ->whereNotIn('chatroom_id', DB::table('chatrooms')->select('id'))
            ->update([
                'chatroom_id' => DB::table('chatrooms')->value('id'),
            ]);

        DB::table('users')
            ->whereNotIn('chat_status_id', DB::table('chat_statuses')->select('id'))
            ->update([
                'chat_status_id' => DB::table('chat_statuses')->value('id'),
            ]);

        Schema::table('users', function (Blueprint $table): void {
            $table->dropIndex('fk_users_groups_idx');
            $table->foreign('group_id')->references('id')->on('groups')->cascadeOnUpdate();
            $table->foreign('chatroom_id')->references('id')->on('chatrooms')->cascadeOnUpdate();
            $table->foreign('chat_status_id')->references('id')->on('chat_statuses')->cascadeOnUpdate();
        });
    }
};
