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
        Schema::table('apikeys', function (Blueprint $table): void {
            $table->index('content');
            $table->string('name')->after('user_id')->default('deprecated_api_key');
            $table->boolean('can_search')->after('content')->default(true);
            $table->boolean('can_download')->after('can_search')->default(true);
            $table->boolean('can_upload')->after('can_download')->default(true);
            $table->boolean('can_view_user')->after('can_upload')->default(true);
            $table->timestamp('expires_at')->after('can_view_user')->default(today()->addMonths(3));
            $table->timestamp('reminded_expiry_at')->after('expires_at')->nullable();
            $table->timestamp('last_used_at')->after('expires_at')->nullable();

            $table->index(['deleted_at', 'expires_at']);
        });

        Schema::table('apikeys', function (Blueprint $table): void {
            $table->string('name')->change();
            $table->boolean('can_search')->change();
            $table->boolean('can_download')->change();
            $table->boolean('can_upload')->change();
            $table->boolean('can_view_user')->change();
            $table->timestamp('expires_at')->change();
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('api_token');
        });
    }
};
