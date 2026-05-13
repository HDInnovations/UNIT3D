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

use App\Helpers\TorrentHelper;
use App\Models\AutomaticTorrentFreeleech;
use App\Models\Torrent;
use Illuminate\Support\Carbon;

test('approve helper applies automatic torrent freeleech duration', function (): void {
    $this->asAuthenticatedUser();

    $now = now()->startOfSecond();
    Carbon::setTestNow($now);

    try {
        AutomaticTorrentFreeleech::query()->create([
            'position'             => 0,
            'freeleech_percentage' => 100,
            'freeleech_duration'   => 3,
        ]);

        $torrent = Torrent::factory()->create([
            'anon'     => true,
            'free'     => 0,
            'fl_until' => null,
        ]);

        TorrentHelper::approveHelper($torrent->id);

        $torrent->refresh();

        expect($torrent->free)->toBe(100)
            ->and($torrent->fl_until?->equalTo($now->copy()->addDays(3)))->toBeTrue();
    } finally {
        Carbon::setTestNow();
    }
});
