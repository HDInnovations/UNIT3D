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
 * @author     Roardom <roardom@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Http\Middleware;

use App\Jobs\SendDeleteUserMail;
use App\Jobs\SendDisableUserMail;
use App\Jobs\SendMassEmail;
use Closure;
use Illuminate\Notifications\SendQueuedNotifications;
use Illuminate\Support\Facades\Redis;

class RateLimitOutboundMail
{
    /**
     * Process the queued job.
     *
     * @param Closure(object): void $next
     */
    public function handle(SendQueuedNotifications|SendDeleteUserMail|SendDisableUserMail|SendMassEmail $job, Closure $next): void
    {
        Redis::throttle(config('cache.prefix').':outbound-email-limiter')
            ->allow(config('other.mail.allow'))
            ->every(config('other.mail.every'))
            ->then(function () use ($job, $next): void {
                $next($job);
            }, function () use ($job): void {
                $job->release(random_int(60, 300));
            });
    }
}
