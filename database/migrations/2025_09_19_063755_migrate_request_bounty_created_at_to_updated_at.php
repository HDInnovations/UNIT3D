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
        Schema::table('requests', function (Blueprint $table): void {
            $table->timestamp('last_bountied_at')->after('updated_at')->index();
        });

        DB::table('requests')
            ->join('request_bounty', 'requests_id', '=', 'requests.id')
            ->groupBy('requests.id')
            ->update([
                'requests.created_at'       => DB::raw('MIN(request_bounty.created_at)'),
                'requests.last_bountied_at' => DB::raw('MAX(request_bounty.created_at)'),
            ]);
    }
};
