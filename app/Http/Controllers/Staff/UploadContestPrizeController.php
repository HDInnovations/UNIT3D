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
use App\Http\Requests\Staff\StoreUploadContestPrizeRequest;
use App\Http\Requests\Staff\UpdateUploadContestPrizeRequest;
use App\Models\UploadContest;
use App\Models\UploadContestPrize;

class UploadContestPrizeController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUploadContestPrizeRequest $request, UploadContest $uploadContest): \Illuminate\Http\RedirectResponse
    {
        $uploadContest->prizes()->create($request->validated());

        return to_route('staff.upload_contests.edit', [
            'uploadContest' => $uploadContest
        ])
            ->with('success', 'Prize added to upload contest.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUploadContestPrizeRequest $request, UploadContest $uploadContest, UploadContestPrize $prize): \Illuminate\Http\RedirectResponse
    {
        $prize->update($request->validated());

        return to_route('staff.upload_contests.edit', [
            'uploadContest' => $uploadContest
        ])
            ->with('success', 'Prize updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UploadContest $uploadContest, UploadContestPrize $prize): \Illuminate\Http\RedirectResponse
    {
        $prize->delete();

        return to_route('staff.upload_contests.edit', [
            'uploadContest' => $uploadContest
        ])
            ->with('success', 'Prize removed from upload contest.');
    }
}
