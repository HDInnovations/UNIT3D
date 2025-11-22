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

namespace App\Http\Controllers;

use App\Models\Torrent;
use App\Models\UploadContest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UploadContestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('upload-contest.index', [
            'uploadContest' => UploadContest::query()->where('active', '=', true)->orderBy('starts_at')->get(),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, UploadContest $uploadContest): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('upload-contest.show', [
            'uploadContest' => $uploadContest,
            'uploaders'     => Torrent::query()
                ->with('user.group')
                ->where('anon', '=', false)
                ->select(DB::raw('user_id, count(*) as value'))
                ->where('created_at', '>=', $uploadContest->starts_at)
                ->where('created_at', '<=', $uploadContest->ends_at)
                ->groupBy('user_id')
                ->orderByDesc('value')
                ->take(10)
                ->get(),
        ]);
    }
}
