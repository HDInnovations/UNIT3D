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

use App\Enums\UserGroup;
use App\Jobs\SendDeleteUserMail;
use App\Models\Comment;
use App\Models\FailedLoginAttempt;
use App\Models\FreeleechToken;
use App\Models\History;
use App\Models\Like;
use App\Models\Message;
use App\Models\Participant;
use App\Models\Peer;
use App\Models\Post;
use App\Models\PrivateMessage;
use App\Models\Scopes\ApprovedScope;
use App\Models\Thank;
use App\Models\Topic;
use App\Models\Torrent;
use App\Models\User;
use App\Services\Unit3dAnnounce;
use Illuminate\Console\Command;
use Exception;
use Throwable;

class AutoSoftDeleteDisabledUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:softdelete_disabled_users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'User account must be In disabled group for at least x days';

    /**
     * Execute the console command.
     *
     * @throws Exception|Throwable If there is an error during the execution of the command.
     */
    final public function handle(): void
    {
        if (!config('pruning.user_pruning')) {
            return;
        }

        $users = User::query()->whereRelation('group', 'slug', '=', 'disabled')
            ->where('disabled_at', '<', now()->subDays(config('pruning.soft_delete')))
            ->get();

        foreach ($users as $user) {
            $user->update([
                'can_download' => false,
                'group_id'     => UserGroup::PRUNED->value,
                'deleted_by'   => User::SYSTEM_USER_ID,
            ]);

            Torrent::query()->withoutGlobalScope(ApprovedScope::class)->where('user_id', '=', $user->id)->update([
                'user_id' => User::SYSTEM_USER_ID,
            ]);

            Comment::query()->where('user_id', '=', $user->id)->update([
                'user_id' => User::SYSTEM_USER_ID,
            ]);

            Post::query()->where('user_id', '=', $user->id)->update([
                'user_id' => User::SYSTEM_USER_ID,
            ]);

            Topic::query()->where('first_post_user_id', '=', $user->id)->update([
                'first_post_user_id' => User::SYSTEM_USER_ID,
            ]);

            Topic::query()->where('last_post_user_id', '=', $user->id)->update([
                'last_post_user_id' => User::SYSTEM_USER_ID,
            ]);

            PrivateMessage::query()->where('sender_id', '=', $user->id)->update([
                'sender_id' => User::SYSTEM_USER_ID,
            ]);

            Participant::query()->where('user_id', '=', $user->id)->delete();
            Message::query()->where('user_id', '=', $user->id)->delete();
            Like::query()->where('user_id', '=', $user->id)->delete();
            Thank::query()->where('user_id', '=', $user->id)->delete();
            Peer::query()->where('user_id', '=', $user->id)->delete();
            History::query()->where('user_id', '=', $user->id)->delete();
            FailedLoginAttempt::query()->where('user_id', '=', $user->id)->delete();

            // Removes all follows for user
            $user->followers()->detach();
            $user->following()->detach();

            // Removes all FL Tokens for user
            foreach (FreeleechToken::query()->where('user_id', '=', $user->id)->get() as $token) {
                $token->delete();
                cache()->forget('freeleech_token:'.$user->id.':'.$token->torrent_id);
            }

            cache()->forget('user:'.$user->passkey);

            Unit3dAnnounce::removeUser($user);

            dispatch(new SendDeleteUserMail($user));

            $user->delete();
        }

        $this->comment('Automated soft delete disabled users command complete');
    }
}
