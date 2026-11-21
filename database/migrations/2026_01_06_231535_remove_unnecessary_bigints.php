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
        Schema::table('achievement_progress', function (Blueprint $table): void {
            $table->dropForeign(['achievement_id']);
        });

        Schema::table('achievement_details', function (Blueprint $table): void {
            $table->increments('id')->change();
        });

        Schema::table('achievement_progress', function (Blueprint $table): void {
            $table->unsignedInteger('achievement_id')->change();
            $table->foreign('achievement_id')->references('id')->on('achievement_details');
        });

        Schema::table('automatic_torrent_freeleeches', function (Blueprint $table): void {
            $table->unsignedInteger('resolution_id')->nullable()->change();
        });

        Schema::table('blacklist_clients', function (Blueprint $table): void {
            $table->increments('id')->change();
        });

        Schema::table('comments', function (Blueprint $table): void {
            $table->dropForeign(['parent_id']);
        });

        Schema::table('comments', function (Blueprint $table): void {
            $table->increments('id')->change();
            $table->unsignedInteger('parent_id')->nullable()->change();

            $table->foreign('parent_id')->references('id')->on('comments')->cascadeOnDelete();
        });

        Schema::table('distributors', function (Blueprint $table): void {
            $table->increments('id')->change();
        });

        Schema::table('donation_packages', function (Blueprint $table): void {
            $table->unsignedInteger('invite_value')->nullable()->change();
            $table->unsignedInteger('donor_value')->nullable()->change();
        });

        Schema::table('groups', function (Blueprint $table): void {
            $table->unsignedInteger('min_uploads')->nullable()->change();
        });

        Schema::table('media_languages', function (Blueprint $table): void {
            $table->increments('id')->change();
        });

        Schema::table('playlist_suggestions', function (Blueprint $table): void {
            $table->dropForeign(['playlist_id']);
        });

        Schema::table('playlists', function (Blueprint $table): void {
            $table->increments('id')->change();
        });

        Schema::table('playlist_suggestions', function (Blueprint $table): void {
            $table->unsignedInteger('playlist_id')->change();
            $table->foreign('playlist_id')->references('id')->on('playlists')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('regions', function (Blueprint $table): void {
            $table->increments('id')->change();
        });

        Schema::table('subtitles', function (Blueprint $table): void {
            $table->increments('id')->change();
        });

        Schema::table('ticket_attachments', function (Blueprint $table): void {
            $table->increments('id')->change();
        });

        Schema::table('ticket_categories', function (Blueprint $table): void {
            $table->smallIncrements('id')->change();
        });

        Schema::table('ticket_priorities', function (Blueprint $table): void {
            $table->smallIncrements('id')->change();
        });

        Schema::table('tickets', function (Blueprint $table): void {
            $table->increments('id')->change();
        });

        // cspell:ignore watchlists
        Schema::table('watchlists', function (Blueprint $table): void {
            $table->increments('id')->change();
        });
    }
};
