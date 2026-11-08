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

use App\Enums\ApiScope;
use App\Models\Apikey;
use Closure;
use Illuminate\Http\Request;

class CheckApiScope
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$scopes): mixed
    {
        $apikey = Apikey::query()
            ->where('content', '=', $request->bearerToken())
            ->sole();

        foreach ($scopes as $scope) {
            abort_unless($apikey->$scope, 403);
        }

        return $next($request);
    }

    public static function with(ApiScope ...$scopes): string
    {
        return static::class.':'.implode(',', array_map(fn ($scope) => $scope->value, $scopes));
    }
}
