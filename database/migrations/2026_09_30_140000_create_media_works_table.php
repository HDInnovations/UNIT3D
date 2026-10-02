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
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('media_works', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('identity_key', 191)->unique();
            $table->string('kind', 16)->index();
            $table->string('source', 32)->nullable();
            $table->string('source_id', 64)->nullable();
            $table->string('title');
            $table->longText('description')->nullable();
            $table->text('cover_url')->nullable();
            $table->text('source_url')->nullable();
            $table->json('raw')->nullable();
            $table->json('facets')->nullable();
            $table->timestamp('metadata_updated_at')->nullable();
            $table->text('metadata_error')->nullable();
            $table->timestamps();

            $table->index(['source', 'source_id']);
        });

        Schema::table('torrents', function (Blueprint $table): void {
            $table->unsignedInteger('media_work_id')->nullable()->after('media_variant_id')->index();
            $table->foreign('media_work_id')->references('id')->on('media_works')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('torrents', function (Blueprint $table): void {
            $table->dropForeign(['media_work_id']);
            $table->dropColumn('media_work_id');
        });

        Schema::dropIfExists('media_works');
    }
};
