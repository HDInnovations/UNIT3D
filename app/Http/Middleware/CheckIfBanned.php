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

use App\Models\Group;
use Closure;
use Exception;

class CheckIfBanned
{
    /**
     * Handle an incoming request.
     *
     * @throws Exception
     */
    public function handle(\Illuminate\Http\Request $request, Closure $next, ?string $guard = null): mixed
    {
        $user = $request->user();
        // Redis returns ints as numeric strings!
        $bannedGroupId = (int) cache()->rememberForever('group:banned:id', fn () => Group::where('slug', '=', 'banned')->soleValue('id'));

        if ($user && $user->group_id === $bannedGroupId) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => __('auth.banned'),
                ]);
            }

            if ($request->is('rss/*')) {
                $message = __('auth.banned');
                $now = now()->toRssString();
                $url = config('app.url');

                return response(
                    <<<XML
                    <?xml version="1.0" encoding="UTF-8" ?>
                    <rss version="2.0">
                        <channel>
                            <title>{$message}</title>
                            <link>{$url}</link>
                            <description>{$message}</description>
                            <pubDate>{$now}</pubDate>
                        </channel>
                    </rss>
                    XML,
                    403
                )->header('Content-Type', 'text/xml');
            }

            auth()->logout();
            $request->session()->flush();

            return to_route('login')
                ->withErrors(__('auth.banned'));
        }

        return $next($request);
    }
}
