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
 * @credit     PyR8zdl
 */

namespace App\Console\Commands;

use App\Models\Ban;
use App\Models\Group;
use App\Models\User;
use App\Notifications\UserBan;
use App\Rules\EmailBlacklist;
use App\Services\Unit3dAnnounce;
use Illuminate\Console\Command;
use Exception;
use Throwable;

class AutoBanDisposableUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:ban_disposable_users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ban user if they are using a disposable email';

    /**
     * Execute the console command.
     *
     * @throws Exception|Throwable If there is an error during the execution of the command.
     */
    final public function handle(): void
    {
        if (!cache()->has(config('email-blacklist.cache-key'))) {
            $this->comment('Email blacklist cache key not found. Skipping!');

            return;
        }

        $bannedGroupId = Group::where('slug', '=', 'banned')->soleValue('id');

        User::where('group_id', '!=', $bannedGroupId)->chunkById(100, function ($users) use ($bannedGroupId): void {
            foreach ($users as $user) {
                $v = validator([
                    'email' => $user->email,
                ], [
                    'email' => [
                        'required',
                        'string',
                        'email',
                        'max:70',
                        new EmailBlacklist(),
                    ],
                ]);

                if ($v->fails()) {
                    // If User Is Using A Disposable Email Set The Users Group To Banned
                    $user->update([
                        'group_id'     => $bannedGroupId,
                        'can_download' => 0,
                    ]);

                    // Log The Ban To Ban Log
                    $domain = substr((string) strrchr((string) $user->email, '@'), 1);

                    $ban = Ban::create([
                        'owned_by'     => $user->id,
                        'created_by'   => User::SYSTEM_USER_ID,
                        'ban_reason'   => 'Detected disposable email, '.$domain.' not allowed.',
                        'unban_reason' => '',
                    ]);

                    // Send Email
                    $user->notify(new UserBan($ban));
                }

                cache()->forget('user:'.$user->passkey);

                Unit3dAnnounce::addUser($user);
            }
        });

        $this->comment('Automated user banning command complete');
    }
}
