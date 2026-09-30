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

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Blade;

beforeEach(function (): void {
    config(['app.timezone' => 'UTC', 'app.display_timezone' => 'Europe/Prague']);
});

test('blade echoes UTC timestamps in the display timezone across daylight saving time', function (): void {
    $summer = Carbon::parse('2026-09-29 17:01:00', 'UTC');
    $winter = Carbon::parse('2026-01-15 23:30:00', 'UTC');

    expect(Blade::render('{{ $summer }}|{{ $winter }}', ['summer' => $summer, 'winter' => $winter]))
        ->toBe('2026-09-29 19:01:00|2026-01-16 00:30:00');
});

test('formatting a timestamp in the display timezone leaves the stored UTC value untouched', function (): void {
    $createdAt = Carbon::parse('2026-01-15 23:30:00', 'UTC');

    expect($createdAt->toDisplayTimezone()->format('Y-m-d H:i'))->toBe('2026-01-16 00:30')
        ->and($createdAt->toDateTimeString())->toBe('2026-01-15 23:30:00')
        ->and($createdAt->getTimezone()->getName())->toBe('UTC');
});
