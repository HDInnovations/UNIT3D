<?php
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

namespace App\Http\Controllers\MediaHub;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\PersonalFreeleech;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    /**
     * Display All Movies.
     */
    public function index(): \Illuminate\Contracts\View\Factory | \Illuminate\View\View
    {
        return \view('mediahub.movie.index');
    }

    /**
     * Show A Movie.
     *
     * @param $id
     */
    public function show(Request $request, $id): \Illuminate\Contracts\View\Factory | \Illuminate\View\View
    {
        $user = $request->user();
        $personalFreeleech = PersonalFreeleech::where('user_id', '=', $user->id)->first();
        $movie = Movie::with(['cast', 'collection', 'genres', 'companies'])->findOrFail($id);

        return \view('mediahub.movie.show', [
            'movie'              => $movie,
            'user'               => $user,
            'personal_freeleech' => $personalFreeleech,
        ]);
    }
}
