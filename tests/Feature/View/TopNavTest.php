<?php

declare(strict_types=1);

use App\Models\User;

test('top nav exposes user group requirements from other menu', function (): void {
    $user = User::factory()->create()->load('group');

    $this->actingAs($user);

    $html = view('partials.top-nav', [
        'donationPercentage'    => 0,
        'downloadCount'         => 0,
        'events'                => collect(),
        'hasActiveWarning'      => false,
        'hasUnmoderatedTorrent' => false,
        'hasUnreadNotification' => false,
        'hasUnreadPm'           => false,
        'hasUnreadTicket'       => false,
        'hasUnresolvedReport'   => false,
        'leechCount'            => 0,
        'pages'                 => collect(),
        'peerCount'             => 0,
        'uploadCount'           => 0,
        'user'                  => $user,
    ])->render();

    expect($html)->toContain(route('groups_requirements'))
        ->and($html)->toContain(__('common.user').' '.__('common.groups'));
});
