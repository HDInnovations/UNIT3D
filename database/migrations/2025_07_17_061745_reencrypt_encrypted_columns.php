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
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('seedboxes')
            ->lazyById()
            ->each(function (object $seedbox): void {
                /** @var object{id: int, ip: string} $seedbox */
                DB::table('seedboxes')
                    ->where('id', '=', $seedbox->id)
                    ->update([
                        'ip' => Crypt::encryptString(Crypt::decrypt($seedbox->ip))
                    ]);
            });

        DB::table('donations')
            ->lazyById()
            ->each(function (object $donation): void {
                /** @var object{id: int, transaction: string} $donation */
                DB::table('donations')
                    ->where('id', '=', $donation->id)
                    ->update([
                        'transaction' => Crypt::encryptString(Crypt::decrypt($donation->transaction)),
                    ]);
            });
    }
};
