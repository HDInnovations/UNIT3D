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

namespace App\Console\Commands;

use App\Models\PersonalFreeleech;
use App\Models\User;
use App\Notifications\PersonalFreeleechDeleted;
use App\Services\Unit3dAnnounce;
use Exception;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use Throwable;

#[Signature('auto:remove_personal_freeleech')]
#[Description('Automatically removes a users personal freeleech if it has expired')]
class AutoRemovePersonalFreeleech extends Command
{
    /**
     * Execute the console command.
     *
     * @throws Exception|Throwable If there is an error during the execution of the command.
     */
    final public function handle(): void
    {
        $personalFreeleech = PersonalFreeleech::query()->where('created_at', '<', now()->subDays(1))->get();

        foreach ($personalFreeleech as $pfl) {
            Notification::send(new User(['id' => $pfl->user_id]), new PersonalFreeleechDeleted());

            // Delete The Record From DB
            $pfl->delete();

            cache()->forget('personal_freeleech:'.$pfl->user_id);
            Unit3dAnnounce::removePersonalFreeleech($pfl->user_id);
        }

        $this->comment('Automated removal user personal freeleech command complete');
    }
}
