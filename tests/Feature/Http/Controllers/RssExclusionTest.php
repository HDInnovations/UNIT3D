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

use App\DTO\TorrentSearchFiltersDTO;
use App\Http\Middleware\UpdateLastAction;
use App\Models\Rss;
use App\Models\Torrent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequestsWithRedis;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->withoutMiddleware([
        ThrottleRequestsWithRedis::class,
        UpdateLastAction::class,
    ]);
});

test('private rss feeds persist an excluded uploader when created', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('rss.store'), [
        'name'              => 'No test uploader',
        'excluded_uploader' => 'TEST',
    ]);

    $response->assertRedirect(route('rss.index', ['hash' => 'private']));

    $rss = Rss::query()->where('user_id', '=', $user->id)->sole();

    expect($rss->json_torrent['excluded_uploader'])->toBe('TEST');
});

test('private rss feeds persist an excluded uploader when updated', function (): void {
    $user = User::factory()->create();
    $rss = Rss::query()->create([
        'name'         => 'No test uploader',
        'position'     => 1,
        'user_id'      => $user->id,
        'is_private'   => true,
        'is_torrent'   => true,
        'json_torrent' => [
            'excluded_uploader' => null,
        ],
    ]);

    $response = $this->actingAs($user)->patch(route('rss.update', ['id' => $rss->id]), [
        'excluded_uploader' => 'TEST',
    ]);

    $response->assertRedirect(route('rss.index', ['hash' => 'private']));

    expect($rss->refresh()->json_torrent['excluded_uploader'])->toBe('TEST');
});

test('public rss feeds persist an excluded uploader when created by staff', function (): void {
    $this->asStaffUser();

    $response = $this->post(route('staff.rss.store'), [
        'name'              => 'Public no test uploader',
        'position'          => 1,
        'excluded_uploader' => 'TEST',
    ]);

    $response->assertRedirect(route('staff.rss.index'));

    $rss = Rss::query()->where('name', '=', 'Public no test uploader')->sole();

    expect($rss->json_torrent['excluded_uploader'])->toBe('TEST')
        ->and($rss->is_private)->toBeFalse();
});

test('torrent search filters exclude uploader usernames', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user);

    $filters = new TorrentSearchFiltersDTO(excludedUploader: 'TEST');
    $sqlQuery = Torrent::query()->where($filters->toSqlQueryBuilder());

    expect($filters->toMeilisearchFilter())
        ->toContain('user.username != "TEST"')
        ->and($sqlQuery->toSql())->toContain('not exists')
        ->and($sqlQuery->getBindings())->toContain('TEST');
});
