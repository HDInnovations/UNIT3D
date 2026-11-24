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
        Schema::create('bot_transactions', function (Blueprint $table): void {
            $table->integer('id', true);
            $table->string('type')->default('')->nullable()->index();
            $table->double('cost')->default(0.00);
            $table->integer('user_id')->default(0)->index();
            $table->integer('bot_id')->default(0)->index();
            $table->boolean('to_user')->default(0)->index();
            $table->boolean('to_bot')->default(0)->index();
            $table->text('comment');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->restrictOnUpdate()->cascadeOnDelete();
            $table->foreign('bot_id')->references('id')->on('bots')->restrictOnUpdate()->cascadeOnDelete();
        });
    }
};
