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

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StorePostTipRequest;
use App\Models\PostTip;
use App\Models\User;
use App\Notifications\NewPostTip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @see \Tests\Feature\Http\Controllers\BonusControllerTest
 */
class PostTipController extends Controller
{
    /**
     * Show previous tip history.
     */
    public function index(Request $request, User $user): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        abort_unless($request->user()->is($user) || $request->user()->group->is_modo, 403);

        return view('user.post-tip.index', [
            'user' => $user,
            'tips' => PostTip::query()
                ->with([
                    'sender.group',
                    'recipient.group',
                    'post.topic'
                ])
                ->where('sender_id', '=', $user->id)
                ->orWhere('recipient_id', '=', $user->id)
                ->latest()
                ->paginate(25),
            'bon'          => $user->formatted_seedbonus,
            'sentTips'     => $user->sentPostTips()->sum('bon'),
            'receivedTips' => $user->receivedPostTips()->sum('bon'),
        ]);
    }

    /**
     * Tip Points To A User.
     *
     * @param User $user The tipping user.
     */
    public function store(StorePostTipRequest $request, User $user): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->is($user), 403);

        DB::transaction(static function () use ($request): void {
            $tip = PostTip::query()->create($request->validated());

            User::query()->whereKey($tip->sender_id)->decrement('seedbonus', (float) $tip->bon);
            User::query()->whereKey($tip->recipient_id)->increment('seedbonus', (float) $tip->bon);

            $tip->recipient->notify((new NewPostTip($tip))->afterCommit());
        });

        return redirect()->back()->with('success', trans('bon.success-tip'));
    }
}
