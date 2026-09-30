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

use App\Http\Middleware\TrustProxies;
use Illuminate\Http\Request;

function clientIpFor(string $remoteAddress, string $forwardedFor): ?string
{
    $request = Request::create('/', 'GET', server: [
        'REMOTE_ADDR'          => $remoteAddress,
        'HTTP_X_FORWARDED_FOR' => $forwardedFor,
    ]);

    new TrustProxies()->handle($request, fn () => null);

    return $request->ip();
}

test('client supplied forwarded entries before the edge proxy address are ignored', function (): void {
    // Caddy forwards the verified client address; nginx appends its Docker gateway hop.
    expect(clientIpFor('172.22.0.5', '203.0.113.99, 198.51.100.7, 172.22.0.1'))->toBe('198.51.100.7');
});

test('forwarded headers from a peer outside the container network are not trusted', function (): void {
    expect(clientIpFor('198.51.100.7', '203.0.113.99'))->toBe('198.51.100.7');
});
