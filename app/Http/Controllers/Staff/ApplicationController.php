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

use App\Enums\ModerationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\ApproveApplicationRequest;
use App\Http\Requests\Staff\RejectApplicationRequest;
use App\Mail\DenyApplication;
use App\Mail\InviteUser;
use App\Models\Application;
use App\Models\Invite;
use App\Models\Scopes\ApprovedScope;
use Illuminate\Support\Facades\Mail;
use Ramsey\Uuid\Uuid;
use Exception;
use Illuminate\Http\Request;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\Staff\ApplicationControllerTest
 */
class ApplicationController extends Controller
{
    /**
     * Display All Applications.
     */
    public function index(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
    {
        return view('Staff.application.index', [
            'applications' => Application::query()
                ->withoutGlobalScopes()
                ->with([
                    'user.group',
                    'moderated.group',
                    'imageProofs',
                    'urlProofs'
                ])
                ->when($request->filled('email'), fn ($query) => $query->where('email', 'LIKE', '%'.$request->email.'%'))
                ->when($request->filled('status'), fn ($query) => $query->where('status', '=', $request->status))
                ->orderBy($request->sortField ?? 'created_at', $request->sortDirection ?? 'desc')
                ->paginate($request->integer('perPage', 25)),
        ])->fragmentIf($request->hasHeader('UNIT3D-Request'), 'application-search');
    }

    /**
     * Get A Application.
     */
    public function show(int $id): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.application.show', [
            'application' => Application::withoutGlobalScope(ApprovedScope::class)->with(['user', 'moderated', 'imageProofs', 'urlProofs'])->findOrFail($id)
        ]);
    }

    /**
     * Delete an application.
     */
    public function destroy(Request $request, int $id): void
    {
        abort_unless($request->user()->group->is_modo, 403);

        $application = Application::withoutGlobalScopes()->findOrFail($id);
        $application->urlProofs()->delete();
        $application->imageProofs()->delete();
        $application->delete();

        $this->dispatch('success', type: 'success', message: 'Application has successfully been deleted!');
    }

    /**
     * Approve A Application.
     *
     * @throws Exception
     */
    public function approve(ApproveApplicationRequest $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $application = Application::withoutGlobalScope(ApprovedScope::class)->findOrFail($id);

        $application->status = ModerationStatus::APPROVED;
        $application->moderated_at = now();
        $application->moderated_by = $request->user()->id;
        $application->save();

        $invite = Invite::create([
            'user_id'    => $request->user()->id,
            'email'      => $application->email,
            'code'       => Uuid::uuid4()->toString(),
            'expires_on' => now()->addDays(config('other.invite_expire')),
            'custom'     => $request->string('approve'),
        ]);

        Mail::to($application->email)->send(new InviteUser($invite));

        return to_route('staff.applications.index')
            ->with('success', 'Application approved');
    }

    /**
     * Reject A Application.
     */
    public function reject(RejectApplicationRequest $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $application = Application::withoutGlobalScope(ApprovedScope::class)->findOrFail($id);
        $application->update([
            'status'       => ModerationStatus::REJECTED,
            'moderated_at' => now(),
            'moderated_by' => $request->user()->id,
        ]);

        Mail::to($application->email)->send(new DenyApplication($request->deny));

        return to_route('staff.applications.index')
            ->with('success', 'Application rejected');
    }
}
