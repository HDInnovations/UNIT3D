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

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table): void {
            $table->foreign('reporter_id', 'foreign_reporting_user_id')->references('id')->on('users')->restrictOnUpdate()->cascadeOnDelete();
            $table->foreign('staff_id', 'foreign_staff_user_id')->references('id')->on('users')->restrictOnUpdate()->cascadeOnDelete();
        });
    }
};
