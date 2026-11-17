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

use App\Models\Report;
use App\Models\User;

test('destroy returns an ok response', function (): void {
    $report = Report::factory()->create();
    $user = User::factory()->moderator()->create();

    $this->actingAs($user)->delete(route('reports.snooze.destroy', [$report]))
        ->assertRedirectToRoute('reports.show', $report)
        ->assertSessionHas('success', 'Report has been un-snoozed');
    $this->assertDatabaseHas('reports', [
        'id'            => $report->id,
        'snoozed_until' => null,
    ]);
});

test('store returns an ok response', function (): void {
    $report = Report::factory()->create();
    $snoozedUntil = now()->addDay();
    $user = User::factory()->moderator()->create();

    $this->actingAs($user)->post(route('reports.snooze.store', [$report]), [
        'snoozed_until' => $snoozedUntil
    ])
        ->assertRedirectToRoute('reports.show', $report)
        ->assertSessionHas('success', 'Report has been snoozed');

    $this->assertDatabaseHas('reports', [
        'id'            => $report->id,
        'snoozed_until' => $snoozedUntil,
    ]);
});
