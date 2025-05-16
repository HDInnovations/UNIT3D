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
 * @author     Obi-Wana
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
        Schema::create('collectible_categories', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('position')->unique();
            $table->timestamps();
        });

        Schema::create('collectibles', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('category_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('resell')->default(false);
            $table->float('price');
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('collectible_categories');
        });

        Schema::create('collectible_requirements', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('collectible_id');
            $table->unsignedBigInteger('min_uploaded')->nullable();
            $table->unsignedBigInteger('min_seedsize')->nullable();
            $table->unsignedBigInteger('min_avg_seedtime')->nullable();
            $table->decimal('min_ratio', 4, 2)->nullable();
            $table->unsignedBigInteger('min_age')->nullable();

            $table->foreign('collectible_id')->references('id')->on('collectibles')->onDelete('CASCADE');
        });

        Schema::create('collectible_items', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('collectible_id');
            $table->unsignedInteger('user_id')->nullable();
            $table->boolean('active')->default(false);
            $table->timestamps();

            $table->foreign('collectible_id')->references('id')->on('collectibles')->onDelete('CASCADE');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unique(['user_id', 'collectible_id']);
        });

        Schema::create('collectible_offers', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('collectible_id');
            $table->unsignedInteger('user_id');
            $table->float('price');
            $table->dateTime('filled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('collectible_id')->references('id')->on('collectibles')->onDelete('CASCADE');
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('collectible_transactions', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('collectible_id');
            $table->unsignedInteger('seller_id');
            $table->unsignedInteger('buyer_id');
            $table->float('price');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('collectible_id')->references('id')->on('collectibles')->onDelete('CASCADE');
            $table->foreign('seller_id')->references('id')->on('users');
            $table->foreign('buyer_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collectible_transactions');

        Schema::dropIfExists('collectible_offers');

        Schema::dropIfExists('collectible_items');

        Schema::dropIfExists('collectible_requirements');

        Schema::dropIfExists('collectibles');

        Schema::dropIfExists('collectible_categories');
    }
};
