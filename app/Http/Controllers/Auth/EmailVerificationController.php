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

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Services\Unit3dAnnounce;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    /**
     * Receive email verification url.
     */
    public function show(EmailVerificationRequest $request): \Illuminate\Http\RedirectResponse
    {
        $request->fulfill();

        $user = $request->user()->load('group:id,slug');

        if ($user->group->slug === 'banned') {
            return to_route('login')
                ->withErrors(trans('auth.activation-error'));
        }

        if ($user->group->slug === 'validating') {
            $user->can_download = true;
            $user->group_id = Group::query()->where('slug', '=', 'user')->soleValue('id');
            $user->save();

            cache()->forget('user:'.$user->passkey);

            Unit3dAnnounce::addUser($user);
        }

        // Check if user has read the rules
        if ($user->read_rules == 0) {
            return redirect()->to(config('other.rules_url'))
                ->with('success', trans('auth.activation-success'))
                ->with('warning', trans('auth.require-rules'));
        }

        return to_route('login')
            ->with('success', trans('auth.activation-success'));
    }

    /**
     * Show verify email message.
     */
    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('auth.verify-email');
    }

    /**
     * Receive registration form.
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('message', 'Verification email sent!');
    }
}
