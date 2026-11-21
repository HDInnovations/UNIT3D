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
use App\Http\Livewire\ApplicationSearch;
use App\Models\Application;

test('approve returns an ok response', function (): void {
    $application = Application::factory()->create([
        'status' => ModerationStatus::PENDING,
    ]);

    $this->post(route('staff.applications.approve', ['id' => $application->id]), [
        'status'       => ModerationStatus::APPROVED->value,
        'moderated_at' => now(),
        'approve'      => 'Approved',
        'email'        => $application->email,
    ])
        ->assertRedirect(route('staff.applications.index'))
        ->assertSessionHas('success', 'Application approved');
});

test('index returns an ok response', function (): void {
    $response = $this->get(route('staff.applications.index'))
        ->assertOk()
        ->assertViewIs('Staff.application.index')
        ->assertSeeLivewire(ApplicationSearch::class);
});

test('reject returns an ok response', function (): void {
    $application = Application::factory()->create([
        'status' => ModerationStatus::PENDING,
    ]);

    $this->post(route('staff.applications.reject', ['id' => $application->id]), [
        'status'       => ModerationStatus::REJECTED->value,
        'moderated_at' => now(),
        'deny'         => 'Denied',
    ])
        ->assertRedirect(route('staff.applications.index'))
        ->assertSessionHas('success', 'Application rejected');
});

test('show returns an ok response', function (): void {
    $application = Application::factory()->create([
        'status' => ModerationStatus::APPROVED,
    ]);

    $this->get(route('staff.applications.show', ['id' => $application->id]))
        ->assertOk()
        ->assertViewIs('Staff.application.show')
        ->assertViewHas('application');
});
