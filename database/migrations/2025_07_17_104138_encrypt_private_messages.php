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
        DB::table('private_messages')
            ->lazyById()
            ->each(function (object $privateMessage): void {
                /** @var object{id: int, message: string} $privateMessage  */
                DB::table('private_messages')
                    ->where('id', '=', $privateMessage->id)
                    ->update([
                        'message' => Crypt::encryptString($privateMessage->message),
                    ]);
            });
    }
};
