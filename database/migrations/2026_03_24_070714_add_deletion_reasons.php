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
        // TODO: Populate this table
        Schema::create('torrent_deletion_reasons', function (Blueprint $table): void {
            $table->smallIncrements('id');
            $table->string('name');
            $table->string('description');
            $table->datetimes();
        });

        Schema::table('torrents', function (Blueprint $table): void {
            $table->unsignedSmallInteger('deletion_reason_id')->nullable()->after('deleted_at');
            $table->text('deletion_reason_extra')->nullable()->after('deletion_reason_id');
            $table->unsignedInteger('trumped_by')->nullable()->after('deletion_reason_extra');

            $table->foreign('trumped_by')->references('id')->on('torrents')->cascadeOnUpdate();
            $table->foreign('deletion_reason_id')->references('id')->on('torrent_deletion_reasons')->cascadeOnUpdate();
        });
    }
};
