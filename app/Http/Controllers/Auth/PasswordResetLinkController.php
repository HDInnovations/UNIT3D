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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class PasswordResetLinkController extends Controller
{
    /**
     * Show form to submit to receive new password reset link.
     */
    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('auth.forgot-password');
    }

    /**
     * Send a new password reset link.
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate(['email' => 'required|email']);

        $_status = Password::sendResetLink(
            $request->only('email')
        );

        // Return successful status regardless of if the user exists or if they're throttled or not.
        // (Account enumeration)
        return back()->with(['status' => __('passwords.sent')]);
    }
}
