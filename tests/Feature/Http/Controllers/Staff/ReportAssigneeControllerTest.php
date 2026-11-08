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

use App\Models\Group;
use App\Models\Report;
use App\Models\User;
use App\Notifications\NewReportAssigned;

test('assign a staff member to a report returns an ok response', function (): void {
    Notification::fake();

    $report = Report::factory()->create();
    $group = Group::factory()->create([
        'is_modo' => 1,
    ]);
    $staff1 = User::factory()->create([
        'group_id' => $group->id,
    ]);
    $staff2 = User::factory()->create([
        'group_id' => $group->id,
    ]);

    $response = $this->actingAs($staff1)->post(route('reports.assignee.store', [$report]), [
        'staff_id' => $staff2->id,
    ]);

    $response->assertRedirect(route('reports.show', $report));

    $this->assertDatabaseHas('reports', [
        'id'       => $report->id,
        'staff_id' => $staff2->id,
    ]);

    Notification::assertSentTo(
        [$staff2],
        NewReportAssigned::class
    );
    Notification::assertCount(1);
});
