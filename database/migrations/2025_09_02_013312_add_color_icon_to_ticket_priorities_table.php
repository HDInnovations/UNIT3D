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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ticket_priorities', function (Blueprint $table): void {
            $table->string('color')->after('position');
            $table->string('icon')->after('color');
        });

        DB::table('ticket_priorities')->where('name', '=', 'Low')->update(['color' => '#FFDC00', 'icon' => config('other.font-awesome').' fa-circle']);
        DB::table('ticket_priorities')->where('name', '=', 'Medium')->update(['color' => '#FF851B', 'icon' => config('other.font-awesome').' fa-circle']);
        DB::table('ticket_priorities')->where('name', '=', 'High')->update(['color' => '#FF4136', 'icon' => config('other.font-awesome').' fa-circle']);
    }
};
