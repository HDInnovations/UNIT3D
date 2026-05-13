<?php

declare(strict_types=1);

use App\Enums\ModerationStatus;
use App\Models\Torrent;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

test('pending resurrection button is highlighted for current user', function (): void {
    config([
        'other.thanks-system.is-enabled' => false,
        'torrent.magnet'                 => false,
    ]);
    Storage::fake('torrent-files');

    $user = User::factory()->create()->load('group');
    $user->setRelation('playlists', collect());
    $this->actingAs($user);

    $torrent = Torrent::factory()->create([
        'free'   => 100,
        'status' => ModerationStatus::APPROVED,
    ]);
    $torrent->setAttribute('bookmarks_count', 0);
    $torrent->setAttribute('bookmarks_exists', false);
    $torrent->setAttribute('freeleechToken_exists', false);
    $torrent->setAttribute('resurrections_exists', true);
    $torrent->setAttribute('resurrected_by_current_user', true);
    $torrent->setAttribute('trump_exists', false);
    $torrent->setAttribute('unsolvedReports', 0);
    $torrent->setRelation('files', collect());
    $torrent->setRelation('history', collect());

    $html = view('torrent.partials.buttons', [
        'fileTree'           => [],
        'personal_freeleech' => false,
        'torrent'            => $torrent,
        'user'               => $user,
    ])->render();

    expect($html)->toContain('torrent-activity-indicator--seeding')
        ->and($html)->toContain('You requested this resurrection');
});
