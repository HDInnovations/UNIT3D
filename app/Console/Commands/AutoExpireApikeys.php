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

use App\Models\Apikey;
use App\Notifications\ApikeyExpire;
use App\Notifications\ApikeyExpireReminder;
use Illuminate\Console\Command;
use Exception;
use Throwable;

class AutoExpireApikeys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:expire_apikeys';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reminds and expires apikeys';

    /**
     * Execute the console command.
     *
     * @throws Exception|Throwable If there is an error during the execution of the command.
     */
    final public function handle(): void
    {
        Apikey::query()
            ->with('user')
            ->where('expires_at', '<', now()->addWeek())
            ->whereNull('reminded_expiry_at')
            ->each(function (Apikey $apikey): void {
                $apikey->user->notify(new ApikeyExpireReminder($apikey->name));
                $apikey->update(['reminded_expiry_at' => now()]);
            });

        Apikey::query()
            ->with('user')
            ->where('expires_at', '<', now())
            ->whereNotNull('reminded_expiry_at')
            ->each(function (Apikey $apikey): void {
                $apikey->user->notify(new ApikeyExpire($apikey->name));
                $apikey->delete();
            });

        $this->comment('Automated expire apikeys command complete');
    }
}
