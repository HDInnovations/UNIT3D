<?php

declare(strict_types=1);

use App\Enums\ModerationStatus;
use App\Models\History;
use App\Models\Torrent;
use App\Models\TorrentReseed;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

test('recent reseed request date is shown as a disabled torrent button', function (): void {
    $requestedAt = now()->subDays(10)->setSecond(0)->setMicrosecond(0);

    $html = renderTorrentButtonsWithReseedRequest($this, $requestedAt);

    expect($html)->toContain(__('torrent.reseed-requested-on', ['datetime' => $requestedAt->toDayDateTimeString()]))
        ->and($html)->toContain('disabled')
        ->and($html)->not->toContain(__('torrent.request-reseed'));
});

test('older reseed request date is shown on a clickable torrent button', function (): void {
    $requestedAt = now()->subDays(31)->setSecond(0)->setMicrosecond(0);

    $html = renderTorrentButtonsWithReseedRequest($this, $requestedAt);

    expect($html)->toContain(__('torrent.reseed-requested-on', ['datetime' => $requestedAt->toDayDateTimeString()]))
        ->and($html)->toContain(route('reseed', ['id' => 1], false))
        ->and($html)->not->toContain('disabled');
});

test('torrent query can select the latest reseed request date', function (): void {
    $torrent = Torrent::factory()->create();
    $user = User::factory()->create();
    $olderRequest = now()->subDays(8);
    $latestRequest = now()->subDays(2)->setSecond(0)->setMicrosecond(0);

    TorrentReseed::create([
        'torrent_id'     => $torrent->id,
        'user_id'        => $user->id,
        'requests_count' => 1,
        'created_at'     => $olderRequest,
        'updated_at'     => $olderRequest,
    ]);

    TorrentReseed::create([
        'torrent_id'     => $torrent->id,
        'user_id'        => User::factory()->create()->id,
        'requests_count' => 1,
        'created_at'     => $latestRequest,
        'updated_at'     => $latestRequest,
    ]);

    $loadedTorrent = Torrent::query()
        ->withMax('reseeds as latest_reseed_requested_at', 'created_at')
        ->withCasts([
            'latest_reseed_requested_at' => 'datetime',
        ])
        ->findOrFail($torrent->id);

    expect($loadedTorrent->latest_reseed_requested_at)->toBeInstanceOf(Carbon::class)
        ->and($loadedTorrent->latest_reseed_requested_at->equalTo($latestRequest))->toBeTrue();
});

function renderTorrentButtonsWithReseedRequest(Tests\TestCase $test, Carbon $requestedAt): string
{
    config([
        'other.thanks-system.is-enabled' => false,
        'torrent.magnet'                 => false,
    ]);
    Storage::fake('torrent-files');

    $user = User::factory()->create()->load('group');
    $user->setRelation('playlists', collect());
    $test->actingAs($user);

    $torrent = Torrent::factory()->create([
        'id'         => 1,
        'free'       => 100,
        'seeders'    => 1,
        'status'     => ModerationStatus::APPROVED,
        'created_at' => now(),
    ]);
    $history = History::factory()->make([
        'active'     => true,
        'seeder'     => false,
        'torrent_id' => $torrent->id,
        'user_id'    => $user->id,
    ]);

    $torrent->setAttribute('bookmarks_count', 0);
    $torrent->setAttribute('bookmarks_exists', false);
    $torrent->setAttribute('freeleechToken_exists', false);
    $torrent->setAttribute('latest_reseed_requested_at', $requestedAt);
    $torrent->setAttribute('resurrections_exists', false);
    $torrent->setAttribute('trump_exists', false);
    $torrent->setAttribute('unsolvedReports', 0);
    $torrent->setRelation('files', collect());
    $torrent->setRelation('history', collect([$history]));

    return view('torrent.partials.buttons', [
        'fileTree'           => [],
        'personal_freeleech' => false,
        'torrent'            => $torrent,
        'user'               => $user,
    ])->render();
}
