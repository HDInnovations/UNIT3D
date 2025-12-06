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
 * @author     Obi-wana
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\StoreUploadContestRequest;
use App\Http\Requests\Staff\UpdateUploadContestRequest;
use App\Models\UploadContest;

class UploadContestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.upload-contest.index', [
            'uploadContests' => UploadContest::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.upload-contest.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUploadContestRequest $request): \Illuminate\Http\RedirectResponse
    {
        UploadContest::create($request->validated());

        return to_route('staff.upload_contests.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UploadContest $uploadContest): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.upload-contest.edit', [
            'uploadContest' => $uploadContest->load('prizes'),
            'prizes'        => $uploadContest->prizes->sortBy('position'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUploadContestRequest $request, UploadContest $uploadContest): \Illuminate\Http\RedirectResponse
    {
        $uploadContest->update($request->validated());

        return to_route('staff.upload_contests.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UploadContest $uploadContest): \Illuminate\Http\RedirectResponse
    {
        $uploadContest->delete();

        return to_route('staff.upload_contests.index');
    }
}
