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
            $table->timestamp('bumped_at')->after('updated_at')->index();
        });

        DB::table('requests')
            ->leftJoinSub(
                DB::table('request_bounty')
                    ->select([
                        'requests_id',
                        DB::raw('MIN(created_at) AS min_created_at'),
                        DB::raw('MAX(created_at) AS max_created_at'),
                    ])
                    ->groupBy('requests_id'),
                'bounties',
                fn ($join) => $join->on('requests_id', '=', 'requests.id'),
            )
            ->update([
                'requests.created_at' => DB::raw('COALESCE(bounties.min_created_at, NOW())'),
                'requests.bumped_at'  => DB::raw('COALESCE(bounties.max_created_at, NOW())'),
            ]);
    }
};
