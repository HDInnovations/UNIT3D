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

namespace App\Http\Controllers\Auth;

use App\Models\Chatroom;
use App\Models\ChatStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\StoreRegisteredUserRequest;
use App\Models\Group;
use App\Models\Invite;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisteredUserController extends Controller
{
    /**
     * Show registration form.
     */
    public function create(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        if ($request->missing('code')) {
            return view('auth.register');
        }

        return view('auth.register', ['code' => $request->query('code')]);
    }

    /**
     * Receive registration form.
     */
    public function store(StoreRegisteredUserRequest $request): \Illuminate\Http\RedirectResponse
    {
        $request->validated();

        $user = User::query()->create([
            'username'    => $request->username,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'passkey'     => md5(random_bytes(60)),
            'rsskey'      => md5(random_bytes(60)),
            'uploaded'    => config('other.default_upload'),
            'downloaded'  => config('other.default_download'),
            'group_id'    => Group::query()->where('slug', '=', 'validating')->soleValue('id'),
            'chatroom_id' => Chatroom::query()
                ->when(
                    \is_int(config('chat.system_chatroom')),
                    fn ($query) => $query->where('id', '=', config('chat.system_chatroom')),
                    fn ($query) => $query->where('name', '=', config('chat.system_chatroom')),
                )
                ->soleValue('id'),
            'chat_status_id' => ChatStatus::query()->value('id'),
        ]);

        $user->passkeys()->create(['content' => $user->passkey]);

        $user->rsskeys()->create(['content' => $user->rsskey]);

        $user->emailUpdates()->create();

        if (config('other.invite-only') === true) {
            $invite = Invite::query()->where('code', '=', $request->code)->first();
            $invite->update([
                'accepted_by' => $user->id,
                'accepted_at' => now(),
            ]);

            if ($invite->internal_note !== null) {
                $user->notes()->create([
                    'message'  => $invite->internal_note,
                    'staff_id' => $invite->user_id,
                ]);
            }
        }

        event(new Registered($user));

        Auth::login($user);

        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        return to_route('verification.notice');
    }
}
