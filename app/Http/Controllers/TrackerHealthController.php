<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\TrackerHealth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Polled by the PVE healthcheck. Without a configured token the endpoint does
 * not exist (404), and a wrong token is indistinguishable from that, so the
 * route reveals nothing to scanners.
 */
final class TrackerHealthController extends Controller
{
    public function __invoke(Request $request, TrackerHealth $health): JsonResponse
    {
        $token = (string) config('capacity.health.token');

        abort_if($token === '' || !hash_equals($token, (string) $request->bearerToken()), 404);

        $result = $health->check();

        return response()->json($result, $result['healthy'] ? 200 : 503, ['Cache-Control' => 'no-store']);
    }
}
