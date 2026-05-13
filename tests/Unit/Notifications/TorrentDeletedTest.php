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

use App\Models\Torrent;
use App\Models\User;
use App\Notifications\TorrentDeleted;
use App\Notifications\TorrentsDeleted;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

afterEach(function (): void {
    Carbon::setTestNow();
});

test('torrent deleted notification includes the users last announce timestamp', function (): void {
    Carbon::setTestNow(Carbon::parse('2026-05-13 12:00:00 UTC'));

    $lastAnnouncedAt = Carbon::parse('2026-05-13 09:00:00 UTC');

    $notification = new TorrentDeleted(
        new Torrent(['id' => 10, 'name' => 'Ubuntu ISO']),
        'Dupe',
        [7 => $lastAnnouncedAt->toJSON()],
    );

    $message = $notification->toSystemNotification(new User(['id' => 7]))['message'];

    expect($message)
        ->toContain('Your last announce for this torrent was 3 hours ago')
        ->toContain('2026-05-13 09:00:00 UTC')
        ->toContain('You can remove it from your client if it is still active.');
});

test('bulk torrent deleted notification includes per torrent last announce timestamps', function (): void {
    Carbon::setTestNow(Carbon::parse('2026-05-13 12:00:00 UTC'));

    $firstAnnouncedAt = Carbon::parse('2026-05-13 10:00:00 UTC');
    $secondAnnouncedAt = Carbon::parse('2026-05-13 11:30:00 UTC');

    $notification = new TorrentsDeleted(
        new Collection([
            new Torrent(['id' => 10, 'name' => 'Ubuntu 1080p']),
            new Torrent(['id' => 11, 'name' => 'Ubuntu 2160p']),
        ]),
        'Ubuntu',
        'Dupe',
        [
            7 => [
                10 => $firstAnnouncedAt->toJSON(),
                11 => $secondAnnouncedAt->toJSON(),
            ],
        ],
    );

    $message = $notification->toSystemNotification(new User(['id' => 7]))['message'];

    expect($message)
        ->toContain('[*]Ubuntu 1080p (last announce: 2 hours ago on 2026-05-13 10:00:00 UTC)')
        ->toContain('[*]Ubuntu 2160p (last announce: 30 minutes ago on 2026-05-13 11:30:00 UTC)');
});
