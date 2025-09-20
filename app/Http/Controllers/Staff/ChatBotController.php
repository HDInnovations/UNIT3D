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

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\UpdateChatBotRequest;
use App\Models\Bot;
use Illuminate\Http\Request;
use Exception;

/**
 * @see \Tests\Feature\Http\Controllers\Staff\ChatBotControllerTest
 */
class ChatBotController extends Controller
{
    /**
     * Display a listing of the Bots resource.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view('Staff.chat.bot.index', [
            'bots' => Bot::oldest('position')->get(),
        ]);
    }

    /**
     * Show the form for editing the specified Bot resource.
     */
    public function edit(Request $request, Bot $bot): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view('Staff.chat.bot.edit', [
            'user' => $request->user(),
            'bot'  => $bot,
        ]);
    }

    /**
     * Update the specified Bot resource in storage.
     */
    public function update(UpdateChatBotRequest $request, Bot $bot): \Illuminate\Http\RedirectResponse
    {
        $bot->update($request->validated());

        return to_route('staff.bots.index')
            ->with('success', "The bot has been updated");
    }

    /**
     * Remove the specified Bot resource from storage.
     *
     * @throws Exception
     */
    public function destroy(Bot $bot): \Illuminate\Http\RedirectResponse
    {
        abort_if($bot->is_protected, 403);

        $bot->delete();

        return to_route('staff.bots.index')
            ->with('success', 'The Humans Vs Machines War has begun! Humans: 1 and Bots: 0');
    }

    /**
     * Disable the specified Bot resource in storage.
     */
    public function disable(Bot $bot): \Illuminate\Http\RedirectResponse
    {
        $bot->update([
            'active' => false,
        ]);

        return to_route('staff.bots.index')
            ->with('success', 'The bot has been disabled');
    }

    /**
     * Enable the specified Bot resource in storage.
     */
    public function enable(Bot $bot): \Illuminate\Http\RedirectResponse
    {
        $bot->update([
            'active' => true,
        ]);

        return to_route('staff.bots.index')
            ->with('success', 'The bot has been enabled');
    }
}
