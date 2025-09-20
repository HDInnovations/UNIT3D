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

use App\Enums\ModerationStatus;
use App\Http\Controllers\Staff\ApplicationController;
use App\Http\Livewire\ApplicationSearch;
use App\Http\Requests\Staff\ApproveApplicationRequest;
use App\Http\Requests\Staff\RejectApplicationRequest;
use App\Models\Application;
use App\Models\Group;
use App\Models\User;

beforeEach(function (): void {
    $this->staffUser = User::factory()->create([
        'group_id' => fn () => Group::factory()->create([
            'is_owner' => true,
            'is_admin' => true,
            'is_modo'  => true,
        ])->id,
    ]);
});

test('approve validates with a form request', function (): void {
    $this->assertActionUsesFormRequest(
        ApplicationController::class,
        'approve',
        ApproveApplicationRequest::class
    );
});

test('approve returns an ok response', function (): void {
    $application = Application::factory()->create([
        'status' => ModerationStatus::PENDING,
    ]);

    $response = $this->actingAs($this->staffUser)->post(route('staff.applications.approve', ['id' => $application->id]), [
        'status'       => ModerationStatus::APPROVED->value,
        'moderated_by' => $this->staffUser->id,
        'moderated_at' => now(),
        'approve'      => 'Approved',
        'email'        => $application->email,
    ]);
    $response->assertRedirect(route('staff.applications.index'));
    $response->assertSessionHas('success', 'Application approved');
});

test('index returns an ok response', function (): void {
    $response = $this->actingAs($this->staffUser)->get(route('staff.applications.index'));
    $response->assertOk();
    $response->assertViewIs('Staff.application.index');
    $response->assertSeeLivewire(ApplicationSearch::class);
});

test('reject validates with a form request', function (): void {
    $this->assertActionUsesFormRequest(
        ApplicationController::class,
        'reject',
        RejectApplicationRequest::class
    );
});

test('reject returns an ok response', function (): void {
    $application = Application::factory()->create([
        'status' => ModerationStatus::PENDING,
    ]);

    $response = $this->actingAs($this->staffUser)->post(route('staff.applications.reject', ['id' => $application->id]), [
        'status'       => ModerationStatus::REJECTED->value,
        'moderated_by' => $this->staffUser->id,
        'moderated_at' => now(),
        'deny'         => 'Denied',
    ]);
    $response->assertRedirect(route('staff.applications.index'));
    $response->assertSessionHas('success', 'Application rejected');
});

test('show returns an ok response', function (): void {
    $application = Application::factory()->create([
        'status' => ModerationStatus::APPROVED,
    ]);

    $response = $this->actingAs($this->staffUser)->get(route('staff.applications.show', ['id' => $application->id]));
    $response->assertOk();
    $response->assertViewIs('Staff.application.show');
    $response->assertViewHas('application');
});
