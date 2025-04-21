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
        Schema::table('tmdb_movies', function (Blueprint $table): void {
            $table->string('certification')->nullable()->after('adult');
            $table->json('content_ratings')->nullable()->after('certification');
        });

        Schema::table('tmdb_tv', function (Blueprint $table): void {
            $table->string('certification')->nullable()->after('in_production');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tmdb_movies', function (Blueprint $table): void {
            $table->dropColumn('certification');
            $table->dropColumn('content_ratings');
        });

        Schema::table('tmdb_tv', function (Blueprint $table): void {
            $table->dropColumn('certification');
        });
    }
};
