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

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Models\BonExchange;
use App\Models\BonTransactions;
use App\Models\PersonalFreeleech;
use App\Models\User;
use App\Notifications\PersonalFreeleechCreated;
use App\Services\Unit3dAnnounce;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @see \Tests\Feature\Http\Controllers\BonusControllerTest
 */
class TransactionController extends Controller
{
    /**
     * BonusController Constructor.
     */
    public function __construct(protected \App\Interfaces\ByteUnitsInterface $byteUnits)
    {
    }

    /**
     * Show Bonus Store System.
     */
    public function create(Request $request, User $user): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        abort_unless($request->user()->is($user), 403);

        return view('user.transaction.create', [
            'user'     => $user,
            'bon'      => $user->formatted_seedbonus,
            'activefl' => $user->personalFreeleeches()->exists(),
            'items'    => BonExchange::all(),
        ]);
    }

    /**
     * Exchange Points For A Item.
     */
    public function store(StoreTransactionRequest $request, User $user): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->is($user), 403);

        return DB::transaction(function () use ($request, $user) {
            $user->refresh();
            $bonExchange = BonExchange::query()->findOrFail($request->integer('exchange'));

            if ($bonExchange->cost > $user->seedbonus) {
                return back()->withErrors('Not enough BON.');
            }

            switch (true) {
                case $bonExchange->upload:
                    if (config('other.bon.max-buffer-to-buy-upload') !== null && $user->uploaded - $user->downloaded > config('other.bon.max-buffer-to-buy-upload')) {
                        return back()->withErrors('You already have enough buffer.');
                    }

                    $user->increment('uploaded', $bonExchange->value);

                    break;
                case $bonExchange->download:
                    if ($user->downloaded < $bonExchange->value) {
                        return back()->withErrors('Not enough download.');
                    }

                    $user->decrement('downloaded', $bonExchange->value);

                    break;
                case $bonExchange->personal_freeleech:
                    if (PersonalFreeleech::query()->where('user_id', '=', $user->id)->exists()) {
                        return back()->withErrors('Your previous personal freeleech is still active.');
                    }

                    PersonalFreeleech::query()->create(['user_id' => $user->id]);

                    cache()->put('personal_freeleech:'.$user->id, true);

                    Unit3dAnnounce::addPersonalFreeleech($user->id);

                    $user->notify(new PersonalFreeleechCreated());

                    break;
                case $bonExchange->invite:
                    if (config('other.invites_restriced') && !\in_array($user->group->name, config('other.invite_groups'), true)) {
                        return back()->withErrors(trans('user.invites-disabled-group'));
                    }

                    if ($user->invites >= config('other.max_unused_user_invites', 1)) {
                        return back()->withErrors('You already have the maximum amount of unused invites allowed per user.');
                    }

                    $user->increment('invites', $bonExchange->value);

                    break;
            }

            BonTransactions::query()->create([
                'bon_exchange_id' => $bonExchange->id,
                'name'            => $bonExchange->description,
                'cost'            => $bonExchange->value,
                'sender_id'       => $user->id,
                'torrent_id'      => null,
            ]);

            $user->decrement('seedbonus', $bonExchange->cost);

            return back()->with('success', trans('bon.success'));
        }, 5);
    }
}
