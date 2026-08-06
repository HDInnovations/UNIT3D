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
        Schema::table('comments', function (Blueprint $table): void {
            $table->bigIncrements('id')->change();
            $table->morphs('commentable');
            $table->foreignId('parent_id')->after('user_id')->nullable()->constrained('comments')->onDelete('cascade');
        });

        DB::table('comments')
            ->whereNotNull('torrent_id')
            ->update([
                'commentable_id'   => DB::raw('torrent_id'),
                'commentable_type' => \App\Models\Torrent::class,
            ]);

        DB::table('comments')
            ->whereNotNull('article_id')
            ->update([
                'commentable_id'   => DB::raw('article_id'),
                'commentable_type' => \App\Models\Article::class,
            ]);

        DB::table('comments')
            ->whereNotNull('requests_id')
            ->update([
                'commentable_id'   => DB::raw('requests_id'),
                'commentable_type' => \App\Models\TorrentRequest::class,
            ]);

        DB::table('comments')
            ->whereNotNull('collection_id')
            ->update([
                'commentable_id'   => DB::raw('collection_id'),
                'commentable_type' => 'App\Models\Collection',
            ]);

        DB::table('comments')
            ->whereNotNull('playlist_id')
            ->update([
                'commentable_id'   => DB::raw('playlist_id'),
                'commentable_type' => \App\Models\Playlist::class,
            ]);

        DB::table('comments')
            ->whereNotNull('ticket_id')
            ->update([
                'commentable_id'   => DB::raw('ticket_id'),
                'commentable_type' => \App\Models\Ticket::class,
            ]);

        Schema::table('comments', function (Blueprint $table): void {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            $table->dropForeign('fk_comments_articles_1');
            $table->dropIndex('fk_comments_torrents_1');
            $table->dropIndex('comments_playlist_id_index');
            $table->dropIndex('comments_collection_id_index');
            $table->dropIndex('comments_ticket_id_index');
            $table->dropColumn('torrent_id', 'article_id', 'requests_id', 'collection_id', 'playlist_id', 'ticket_id');
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        });
    }
};
