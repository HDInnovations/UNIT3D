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

namespace App\Console\Commands;

use App\Models\Donation;
use App\Models\Seedbox;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class ReEncrypt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 're-encrypt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Re-encrypt all encrypted columns. Should be ran after rotating the app key.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $models = [
            Donation::class => [
                'transaction',
            ],
            Seedbox::class => [
                'ip',
            ],
        ];

        foreach ($models as $model => $columns) {
            $table = new $model()->getTable();

            $this->comment("Re-encrypting {$table}...");

            DB::table($table)
                ->lazyById()
                ->each(function (object $record) use ($table, $columns): void {
                    if (!property_exists($record, 'id')) {
                        throw new Exception("Column 'id' not found on '{$table}' record.");
                    }

                    $reEncrypted = [];

                    foreach ($columns as $column) {
                        if (!property_exists($record, $column)) {
                            throw new Exception("Column '{$column}' not found on '{$table}' record.");
                        }

                        try {
                            $reEncrypted[$column] = Crypt::encryptString(Crypt::decryptString($record->$column));
                        } catch (\Illuminate\Contracts\Encryption\DecryptException) {
                        }
                    }

                    if ($reEncrypted !== []) {
                        DB::table($table)->where('id', '=', $record->id)->update($reEncrypted);
                    }
                });

            $this->comment("Re-encrypted {$table}.");
        }
    }
}
