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
use App\Http\Requests\StoreGiftRequest;
use App\Models\Gift;
use App\Models\User;
use App\Notifications\NewBon;
use App\Repositories\ChatRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GiftController extends Controller
{
    /**
     * UserGiftController Constructor.
     */
    public function __construct(private readonly ChatRepository $chatRepository)
    {
    }

    /**
     * Show previous gift history.
     */
    public function index(Request $request, User $user): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        abort_unless($request->user()->is($user) || $request->user()->group->is_modo, 403);

        return view('user.gift.index', [
            'user'  => $user,
            'gifts' => Gift::query()
                ->with([
                    'sender.group',
                    'recipient.group',
                ])
                ->where('sender_id', '=', $user->id)
                ->orWhere('recipient_id', '=', $user->id)
                ->latest()
                ->paginate(25),
            'bon'           => $user->formatted_seedbonus,
            'sentGifts'     => $user->sentGifts()->sum('bon'),
            'receivedGifts' => $user->receivedGifts()->sum('bon'),
        ]);
    }

    /**
     * Show gift form.
     */
    public function create(Request $request, User $user): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        abort_unless($request->user()->is($user), 403);

        return view('user.gift.create', [
            'user' => $user,
            'bon'  => $user->formatted_seedbonus,
        ]);
    }

    /**
     * Gift points to a user.
     */
    public function store(StoreGiftRequest $request): \Illuminate\Http\RedirectResponse
    {
        $sender = $request->user();
        $receiver = User::query()->where('username', '=', $request->recipient_username)->sole();

        DB::transaction(function () use ($sender, $receiver, $request): void {
            $sender->decrement('seedbonus', $request->bon);
            $receiver->increment('seedbonus', $request->bon);

            $gift = Gift::query()->create([
                'bon'          => $request->bon,
                'sender_id'    => $sender->id,
                'recipient_id' => $receiver->id,
                'message'      => $request->message,
            ]);

            $receiver->notify((new NewBon($gift))->afterCommit());
        });

        $this->chatRepository->systemMessage(
            \sprintf(
                '[url=%s]%s[/url] has gifted %s BON to [url=%s]%s[/url]',
                href_profile($sender),
                $sender->username,
                $request->bon,
                href_profile($receiver),
                $receiver->username
            )
        );

        return redirect()->back()->with('success', trans('bon.gift-sent'));
    }
}
