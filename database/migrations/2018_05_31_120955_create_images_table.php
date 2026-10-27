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
    public function up(): void
    {
        Schema::create('images', function (Blueprint $table): void {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('album_id')->unsigned();
            $table->string('image');
            $table->string('description');
            $table->string('type');
            $table->integer('downloads')->nullable();
            $table->foreign('album_id')->references('id')->on('albums')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }
};
