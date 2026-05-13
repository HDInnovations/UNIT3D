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

use App\Http\Controllers\Staff\ReportNoteController;
use App\Http\Middleware\UpdateLastAction;
use App\Http\Requests\Staff\StoreReportNoteRequest;
use App\Models\Group;
use App\Models\Report;
use App\Models\ReportNote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequestsWithRedis;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->withoutMiddleware(UpdateLastAction::class);
    $this->withoutMiddleware(ThrottleRequestsWithRedis::class);
    $this->withoutVite();
});

test('store report note validates with a form request', function (): void {
    $this->assertActionUsesFormRequest(
        ReportNoteController::class,
        'store',
        StoreReportNoteRequest::class
    );
});

test('staff can add a note to a report', function (): void {
    $report = Report::factory()->create();
    $staff = User::factory()->create([
        'group_id' => Group::factory()->create(['is_modo' => 1])->id,
    ]);

    $response = $this->actingAs($staff)->post(route('staff.reports.notes.store', ['report' => $report]), [
        'message' => 'Started checking this report.',
    ]);

    $response->assertRedirect(route('staff.reports.show', $report));

    $this->assertDatabaseHas('report_notes', [
        'report_id' => $report->id,
        'user_id'   => $staff->id,
        'message'   => 'Started checking this report.',
    ]);
});

test('report details show staff notes', function (): void {
    $report = Report::factory()->create();
    $staff = User::factory()->create([
        'username' => 'reportstaff',
        'group_id' => Group::factory()->create(['is_modo' => 1])->id,
    ]);

    ReportNote::factory()->create([
        'report_id' => $report->id,
        'user_id'   => $staff->id,
        'message'   => 'Downloaded sample for review.',
    ]);

    $response = $this->actingAs($staff)->get(route('staff.reports.show', [$report]));

    $response->assertOk()
        ->assertSee('Staff Notes')
        ->assertSee('reportstaff')
        ->assertSee('Downloaded sample for review.');
});

test('staff can delete a single report note', function (): void {
    $report = Report::factory()->create();
    $staff = User::factory()->create([
        'group_id' => Group::factory()->create(['is_modo' => 1])->id,
    ]);
    $note = ReportNote::factory()->create([
        'report_id' => $report->id,
    ]);
    $otherNote = ReportNote::factory()->create([
        'report_id' => $report->id,
    ]);

    $response = $this->actingAs($staff)->delete(route('staff.reports.notes.destroy', [
        'report'     => $report,
        'reportNote' => $note,
    ]));

    $response->assertRedirect(route('staff.reports.show', $report));

    $this->assertModelMissing($note);
    $this->assertModelExists($otherNote);
});
