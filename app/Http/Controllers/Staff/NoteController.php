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
use App\Http\Requests\Staff\UpdateNoteRequest;
use App\Models\Note;

/**
 * @see \Tests\Feature\Http\Controllers\Staff\NoteControllerTest
 */
class NoteController extends Controller
{
    /**
     * Display All User Notes.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.note.index');
    }

    /**
     * Update a user note.
     */
    public function update(Note $note, UpdateNoteRequest $request): \Illuminate\Http\RedirectResponse
    {
        $note->update($request->validated());

        return back()->with('success', 'Success');
    }
}
