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

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\DestroyTorrentDeletionReasonRequest;
use App\Http\Requests\Staff\StoreTorrentDeletionReasonRequest;
use App\Http\Requests\Staff\UpdateTorrentDeletionReasonRequest;
use App\Models\TorrentDeletionReason;
use Exception;

class TorrentDeletionReasonController extends Controller
{
    /**
     * Display all torrent deletion reasons.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.torrent-deletion-reason.index', [
            'torrentDeletionReasons' => TorrentDeletionReason::query()->get(),
        ]);
    }

    /**
     * Show torrent deletion reason create form.
     */
    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.torrent-deletion-reason.create');
    }

    /**
     * Store a new torrent deletion reason.
     */
    public function store(StoreTorrentDeletionReasonRequest $request): \Illuminate\Http\RedirectResponse
    {
        TorrentDeletionReason::query()->create($request->validated());

        return to_route('staff.torrent_deletion_reasons.index')
            ->with('success', 'Deletion reason successfully added');
    }

    /**
     * Torrent deletion reason edit form.
     */
    public function edit(TorrentDeletionReason $torrentDeletionReason): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.torrent-deletion-reason.edit', [
            'torrentDeletionReason' => $torrentDeletionReason,
        ]);
    }

    /**
     * Edit a torrent deletion reason.
     */
    public function update(UpdateTorrentDeletionReasonRequest $request, TorrentDeletionReason $torrentDeletionReason): \Illuminate\Http\RedirectResponse
    {
        $torrentDeletionReason->update($request->validated());

        return to_route('staff.torrent_deletion_reasons.index')
            ->with('success', 'Deletion reason successfully modified');
    }

    /**
     * Delete edit form.
     */
    public function delete(TorrentDeletionReason $torrentDeletionReason): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.torrent-deletion-reason.delete', [
            'otherTorrentDeletionReasons' => TorrentDeletionReason::query()->where('id', '!=', $torrentDeletionReason->id)->get(),
            'torrentDeletionReason'       => $torrentDeletionReason,
        ]);
    }

    /**
     * Delete a torrent deletion reason.
     *
     * @throws Exception
     */
    public function destroy(DestroyTorrentDeletionReasonRequest $request, TorrentDeletionReason $torrentDeletionReason): \Illuminate\Http\RedirectResponse
    {
        $torrentDeletionReason->torrents()->update($request->validated());
        $torrentDeletionReason->delete();

        return to_route('staff.torrent_deletion_reasons.index')
            ->with('success', 'Deletion reason successfully deleted');
    }
}
