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
 * @author     Obi-wana
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
        Schema::create('upload_contests', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('name');
            $table->text('description');
            $table->string('icon');
            $table->boolean('active');
            $table->boolean('awarded');
            $table->date('starts_at');
            $table->date('ends_at');
            $table->timestamps();
        });

        Schema::create('upload_contest_prizes', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('upload_contest_id');
            $table->string('type');
            $table->unsignedInteger('amount');
            $table->unsignedInteger('position');

            $table->foreign('upload_contest_id')->references('id')->on('upload_contests');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upload_contest_prizes');
        Schema::dropIfExists('upload_contests');
    }
};
