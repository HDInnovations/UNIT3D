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

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Symfony\Component\HttpFoundation\Request as RequestAlias;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * Only the in-stack nginx container and its Docker gateway hop are trusted;
     * the edge proxy (Caddy) overwrites X-Forwarded-For with the verified client
     * address, so every earlier, client-supplied entry is ignored.
     *
     * @var array<int, string>|string|null
     */
    protected $proxies = ['172.16.0.0/12'];

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers = RequestAlias::HEADER_X_FORWARDED_FOR | RequestAlias::HEADER_X_FORWARDED_HOST | RequestAlias::HEADER_X_FORWARDED_PORT | RequestAlias::HEADER_X_FORWARDED_PROTO | RequestAlias::HEADER_X_FORWARDED_AWS_ELB;
}
