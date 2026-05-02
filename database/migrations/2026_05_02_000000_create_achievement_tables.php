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
        Schema::create('achievements', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('name');
            $table->string('category');
            $table->string('type');
            $table->string('icon_path')->nullable();
            $table->integer('positions')->default(0);
            $table->boolean('is_hidden')->default(false);
            $table->boolean('enabled')->default(true);
            $table->smallInteger('filter_type_id')->unsigned()->nullable();
            $table->smallInteger('filter_category_id')->unsigned()->nullable();
            $table->smallInteger('filter_resolution_id')->unsigned()->nullable();
            $table->unsignedBigInteger('filter_playlist_id')->nullable();
            $table->timestamps();

            $table->foreign('filter_type_id')->references('id')->on('types')->cascadeOnUpdate()->nullOnDelete();
            $table->foreign('filter_category_id')->references('id')->on('categories')->cascadeOnUpdate()->nullOnDelete();
            $table->foreign('filter_resolution_id')->references('id')->on('resolutions')->cascadeOnUpdate()->nullOnDelete();
            $table->foreign('filter_playlist_id')->references('id')->on('playlists')->cascadeOnUpdate()->nullOnDelete();
        });

        Schema::create('achievement_tiers', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('achievement_id');
            $table->smallInteger('tier')->unsigned();
            $table->string('name');
            $table->text('description');
            $table->decimal('threshold', 22, 2);
            $table->string('icon_path')->nullable();
            $table->timestamps();

            $table->foreign('achievement_id')->references('id')->on('achievements')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unique(['achievement_id', 'tier']);
        });

        Schema::create('user_achievements', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('achievement_id');
            $table->smallInteger('current_tier')->unsigned();
            $table->dateTime('achieved_at');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('achievement_id')->references('id')->on('achievements')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unique(['user_id', 'achievement_id']);
            $table->index('achievement_id');
        });

        // Drop legacy tables from the old assada/laravel-achievements package.
        Schema::dropIfExists('achievement_progress');
        Schema::dropIfExists('achievement_details');
    }

    public function down(): void
    {
        Schema::dropIfExists('user_achievements');
        Schema::dropIfExists('achievement_tiers');
        Schema::dropIfExists('achievements');
    }
};
