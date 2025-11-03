<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\VerifyIrckeyRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class IrckeyAuthController extends Controller
{
    public function __invoke(VerifyIrckeyRequest $request): JsonResponse
    {
        $submittedKey = (string) $request->input('irckey');
        $username = (string) $request->input('username');

        $user = User::query()
            ->with(['group:id,slug,name'])
            ->where('username', $username)
            ->first();

        if ($user === null) {
            return response()->json([
                'valid'  => false,
                'reason' => 'invalid_credentials',
            ], 404);
        }

        if ($request->filled('username') && strcasecmp($user->username, (string) $request->string('username')) !== 0) {
            return response()->json([
                'valid'  => false,
                'reason' => 'username_mismatch',
            ], 409);
        }

        // FIXME this doesn't work
        $activeIrckey = $user->irckeys()
//            ->whereNull('deleted_at')
            ->latest('id')
            ->first();

        $currentKey = $user->irckey ?? '';
        if ($currentKey === '' || !hash_equals($currentKey, $submittedKey)) {
            if ($activeIrckey === null || !hash_equals($activeIrckey->content, $submittedKey)) {
                return response()->json([
                    'valid'  => false,
                    'reason' => 'invalid_key',
                ], 401);
            }
        }

        return response()->json([
            'valid' => true,
            'user'  => [
                'id'       => $user->id,
                'username' => $user->username,
                'group'    => [
                    'id'   => $user->group->id,
                    'slug' => $user->group->slug,
                    'name' => $user->group->name,
                ],
                'disabled_at'  => $user->disabled_at?->toIso8601String(),
                'deleted_at'   => $user->deleted_at?->toIso8601String(),
            ],
        ]);
    }
}
