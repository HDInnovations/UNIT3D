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

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreApplicationRequest;
use App\Models\Application;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\Staff\ApplicationControllerTest
 */
class ApplicationController extends Controller
{
    /**
     * Application Add Form.
     */
    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('auth.application.create');
    }

    /**
     * Store A New Application.
     */
    public function store(StoreApplicationRequest $request): \Illuminate\Http\RedirectResponse
    {
        abort_unless(config('other.application_signups'), 403);

        $application = Application::query()->create($request->application->toArray());
        $application->imageProofs()->createMany($request->images->toArray());
        $application->urlProofs()->createMany($request->links->toArray());

        return to_route('login')
            ->with('success', trans('auth.application-submitted'));
    }
}
