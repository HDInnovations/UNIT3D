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
use App\Http\Requests\Staff\StoreBlacklistClientRequest;
use App\Http\Requests\Staff\UpdateBlacklistClientRequest;
use App\Models\BlacklistClient;
use App\Services\Unit3dAnnounce;

/**
 * @see \Tests\Feature\Http\Controllers\Staff\GroupControllerTest
 */
class BlacklistClientController extends Controller
{
    /**
     * Display All Blacklisted Clients.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.blacklist.clients.index', [
            'clients' => BlacklistClient::latest()->get(),
        ]);
    }

    /**
     * Blacklisted Client Edit Form.
     */
    public function edit(BlacklistClient $blacklistClient): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view('Staff.blacklist.clients.edit', [
            'client' => $blacklistClient,
        ]);
    }

    /**
     * Edit A Blacklisted Client.
     */
    public function update(UpdateBlacklistClientRequest $request, BlacklistClient $blacklistClient): \Illuminate\Http\RedirectResponse
    {
        Unit3dAnnounce::removeBlacklistedAgent($blacklistClient);

        $blacklistClient->update([
            // Overriding is necessary to cast empty strings (converted to null by middleware) back into strings
            'peer_id_prefix' => $request->string('peer_id_prefix'),
        ] + $request->validated());

        Unit3dAnnounce::addBlacklistedAgent($blacklistClient);

        cache()->forget('client_blacklist');

        return to_route('staff.blacklisted_clients.index')
            ->with('success', 'Blacklisted client was updated successfully!');
    }

    /**
     * Blacklisted Client Add Form.
     */
    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.blacklist.clients.create');
    }

    /**
     * Store A New Blacklisted Client.
     */
    public function store(StoreBlacklistClientRequest $request): \Illuminate\Http\RedirectResponse
    {
        $client = BlacklistClient::create([
            // Overriding is necessary to cast empty strings (converted to null by middleware) back into strings
            'peer_id_prefix' => $request->string('peer_id_prefix'),
        ] + $request->validated());

        Unit3dAnnounce::addBlacklistedAgent($client);

        cache()->forget('client_blacklist');

        return to_route('staff.blacklisted_clients.index')
            ->with('success', 'Blacklisted client stored successfully!');
    }

    /**
     * Delete A Blacklisted Client.
     */
    public function destroy(BlacklistClient $blacklistClient): \Illuminate\Http\RedirectResponse
    {
        Unit3dAnnounce::removeBlacklistedAgent($blacklistClient);

        $blacklistClient->delete();

        cache()->forget('client_blacklist');

        return to_route('staff.blacklisted_clients.index')
            ->with('success', 'Blacklisted client destroyed successfully!');
    }
}
