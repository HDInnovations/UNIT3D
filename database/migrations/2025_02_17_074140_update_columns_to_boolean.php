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
        DB::table('tickets')->whereNull('user_read')->update([
            'user_read' => false,
        ]);

        DB::table('tickets')->whereNull('staff_read')->update([
            'staff_read' => false,
        ]);

        Schema::table('tickets', function (Blueprint $table): void {
            $table->boolean('user_read')->default(false)->change();
            $table->boolean('staff_read')->default(false)->change();
        });

        Schema::table('comments', function (Blueprint $table): void {
            $table->boolean('anon')->default(false)->change();
        });

        Schema::table('request_claims', function (Blueprint $table): void {
            $table->boolean('anon')->default(false)->change();
        });

        Schema::table('torrents', function (Blueprint $table): void {
            $table->boolean('anon')->default(false)->change();
            $table->boolean('sticky')->default(false)->change();
            $table->boolean('personal_release')->default(false)->change();
        });

        Schema::table('reports', function (Blueprint $table): void {
            $table->boolean('solved')->default(false)->change();
        });
    }
};
