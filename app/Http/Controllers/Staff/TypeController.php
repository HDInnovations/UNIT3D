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
use App\Http\Requests\Staff\StoreTypeRequest;
use App\Http\Requests\Staff\UpdateTypeRequest;
use App\Models\Type;
use Exception;

/**
 * @see \Tests\Feature\Http\Controllers\Staff\TypeControllerTest
 */
class TypeController extends Controller
{
    /**
     * Display All Types.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.type.index', [
            'types' => Type::orderBy('position')->get(),
        ]);
    }

    /**
     * Show Type Create Form.
     */
    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.type.create');
    }

    /**
     * Store A New Type.
     */
    public function store(StoreTypeRequest $request): \Illuminate\Http\RedirectResponse
    {
        Type::create($request->validated());

        return to_route('staff.types.index')
            ->with('success', 'Type successfully added');
    }

    /**
     * Type Edit Form.
     */
    public function edit(Type $type): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.type.edit', [
            'type' => $type,
        ]);
    }

    /**
     * Edit A Type.
     */
    public function update(UpdateTypeRequest $request, Type $type): \Illuminate\Http\RedirectResponse
    {
        $type->update($request->validated());

        return to_route('staff.types.index')
            ->with('success', 'Type successfully modified');
    }

    /**
     * Delete A Type.
     *
     * @throws Exception
     */
    public function destroy(Type $type): \Illuminate\Http\RedirectResponse
    {
        $type->delete();

        return to_route('staff.types.index')
            ->with('success', 'Type successfully deleted');
    }
}
