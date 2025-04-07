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
        Schema::create('tmdb_recommended_movies', function (Blueprint $table): void {
            $table->unsignedInteger('tmdb_movie_id');
            $table->unsignedInteger('recommended_tmdb_movie_id');
            $table->primary(['tmdb_movie_id', 'recommended_tmdb_movie_id']);

            $table->foreign('tmdb_movie_id')->references('id')->on('tmdb_movies')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('recommended_tmdb_movie_id')->references('id')->on('tmdb_movies')->cascadeOnUpdate()->cascadeOnDelete();
        });

        Schema::create('tmdb_recommended_tv', function (Blueprint $table): void {
            $table->unsignedInteger('tmdb_tv_id');
            $table->unsignedInteger('recommended_tmdb_tv_id');
            $table->primary(['tmdb_tv_id', 'recommended_tmdb_tv_id']);

            $table->foreign('tmdb_tv_id')->references('id')->on('tmdb_tv')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('recommended_tmdb_tv_id')->references('id')->on('tmdb_tv')->cascadeOnUpdate()->cascadeOnDelete();
        });

        DB::table('tmdb_recommended_movies')->insertUsing(
            ['tmdb_movie_id', 'recommended_tmdb_movie_id'],
            DB::table('tmdb_recommendations')
                ->select([
                    'tmdb_movie_id',
                    'recommended_tmdb_movie_id',
                ])
                ->whereNotNull('tmdb_movie_id')
                ->whereNotNull('recommended_tmdb_movie_id'),
        );

        DB::table('tmdb_recommended_tv')->insertUsing(
            ['tmdb_tv_id', 'recommended_tmdb_tv_id'],
            DB::table('tmdb_recommendations')
                ->select([
                    'tmdb_tv_id',
                    'recommended_tmdb_tv_id',
                ])
                ->whereNotNull('tmdb_tv_id')
                ->whereNotNull('recommended_tmdb_tv_id'),
        );

        Schema::drop('tmdb_recommendations');
    }
};
