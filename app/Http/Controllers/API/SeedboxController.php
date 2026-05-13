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

namespace App\Http\Controllers\API;

use App\Models\Seedbox;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class SeedboxController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $request->user()
                ->seedboxes()
                ->latest()
                ->get()
                ->map(fn (Seedbox $seedbox): array => $this->serializeSeedbox($seedbox)),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $userSeedboxes = Seedbox::query()->where('user_id', '=', $user->id)->get(['ip', 'name']);
        $seedboxIps = $userSeedboxes->pluck('ip')->filter(fn ($ip) => filter_var($ip, FILTER_VALIDATE_IP) !== false);
        $seedboxNames = $userSeedboxes->pluck('name');

        $validated = $request->validate(
            [
                'name' => [
                    'required',
                    'alpha_num',
                    Rule::notIn($seedboxNames),
                ],
                'ip' => [
                    'bail',
                    'required',
                    'ip',
                    Rule::notIn($seedboxIps),
                ],
            ],
            [
                'name.not_in' => 'You have already used this seedbox name.',
                'ip.not_in'   => 'You have already registered this seedbox IP.',
            ]
        );

        $seedbox = Seedbox::query()->create([
            'user_id' => $user->id,
            'name'    => $validated['name'],
            'ip'      => $validated['ip'],
        ]);

        return response()->json([
            'success' => true,
            'data'    => $this->serializeSeedbox($seedbox),
            'message' => trans('user.seedbox-added-success'),
        ], 201);
    }

    public function destroy(Request $request, int $seedbox): JsonResponse
    {
        $seedbox = Seedbox::query()->findOrFail($seedbox);

        abort_unless((int) $seedbox->user_id === (int) $request->user()->id, 403);

        $seedbox->delete();

        return response()->json([
            'success' => true,
            'message' => trans('user.seedbox-deleted-success'),
        ]);
    }

    /**
     * @return array{id: int, name: string, ip: string, created_at: string|null, updated_at: string|null}
     */
    private function serializeSeedbox(Seedbox $seedbox): array
    {
        return [
            'id'         => $seedbox->id,
            'name'       => $seedbox->name,
            'ip'         => $seedbox->ip,
            'created_at' => $seedbox->created_at?->toISOString(),
            'updated_at' => $seedbox->updated_at?->toISOString(),
        ];
    }
}
