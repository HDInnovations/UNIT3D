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
use App\Models\Torrent;
use App\Models\TorrentRequest;
use App\Models\User;
use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('index returns an ok response', function (): void {
    $reports = Report::factory()->times(3)->create();
    $group = Group::factory()->create([
        'is_modo' => 1,
    ]);
    $user = User::factory()->create([
        'group_id' => $group->id,
    ]);

    $response = $this->actingAs($user)->get(route('reports.index'));

    $response->assertOk();
    $response->assertViewIs('report.index');
});

test('show a report returns an ok response', function (): void {
    $report = Report::factory()->create();
    $group = Group::factory()->create([
        'is_modo' => 1,
    ]);
    $user = User::factory()->create([
        'group_id' => $group->id,
    ]);

    $response = $this->actingAs($user)->get(route('reports.show', [$report]));

    $response->assertOk();
    $response->assertViewIs('report.show');
    $response->assertViewHas('report', $report);
});

test('reply a report returns an ok response', function (): void {
    $report = Report::factory()->create();
    $group = Group::factory()->create([
        'is_modo' => 1,
    ]);
    $user = User::factory()->create([
        'group_id' => $group->id,
    ]);

    $verdictMessage = 'This report has been resolved.';

    $response = $this->actingAs($user)->post(route('reports.replies.store', ['report' => $report]), [
        'content' => $verdictMessage,
    ]);

    $response->assertRedirect(route('reports.show', ['report' => $report]));
    $this->assertDatabaseHas('report_replies', [
        'report_id' => $report->id,
        'content'   => $verdictMessage,
        'user_id'   => $user->id,
    ]);
});

test('request returns an ok response', function (): void {
    $torrentRequest = TorrentRequest::factory()->create();
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('reports.store'), [
        'reported_request_id' => $torrentRequest->id,
        'message'             => 'test',
    ])
        ->assertRedirectBack()
        ->assertSessionHas('success', __('user.report-sent'));
});

test('torrent returns an ok response', function (): void {
    $torrent = Torrent::factory()->create();
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('reports.store'), [
        'reported_torrent_id' => $torrent->id,
        'message'             => 'test',
    ])
        ->assertRedirectBack()
        ->assertSessionHas('success', __('user.report-sent'));
});

test('user returns an ok response', function (): void {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $response = $this->actingAs($user1)->post(route('reports.store'), [
        'reported_user_username' => $user2->username,
        'message'                => 'test',
    ])
        ->assertRedirectBack()
        ->assertSessionHas('success', __('user.report-sent'));
});

test('close returns an ok response', function (): void {
    $report = Report::factory()->create();

    $response = $this->actingAs($report->reporter)->post(route('reports.close', [$report]))
        ->assertRedirectToRoute('reports.index')
        ->assertSessionHas('success', 'Report has been successfully resolved');
});

test('reopen returns an ok response', function (): void {
    $report = Report::factory()->create();

    $response = $this->actingAs($report->reporter)->post(route('reports.reopen', [$report]))
        ->assertRedirectToRoute('reports.index')
        ->assertSessionHas('success', 'Report reopened');

    $this->assertDatabaseHas('reports', [
        'id'        => $report->id,
        'solved_at' => null,
    ]);
});

test('reporter reply returns an ok response', function (): void {
    $report = Report::factory()->create();

    $response = $this->actingAs($report->reporter)->post(route('reports.replies.store', [$report]), [
        'content' => 'test',
    ])
        ->assertRedirectToRoute('reports.show', [$report]);

    $this->assertDatabaseHas('report_replies', [
        'report_id' => $report->id,
        'content'   => 'test',
    ]);
});
