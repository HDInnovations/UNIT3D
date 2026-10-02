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
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Http\Controllers;

use App\Models\UploadDraft;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Private, owner-only named drafts of the torrent upload create form.
 *
 * Only a bounded allowlist of scalar/flag create-form fields is ever
 * accepted or returned; files, passkeys, provider raw payloads, and the
 * CSRF token are never part of a draft. Restoring a draft never implies
 * the user's files or any previously fetched provider metadata are still
 * current; those must be reselected/refetched in the browser.
 */
class UploadDraftController extends Controller
{
    /**
     * Bounded validation rules for every draft field allowed to round-trip.
     * Any key posted outside this allowlist is silently discarded before
     * validation ever sees it.
     *
     * @var array<string, list<string>>
     */
    private const array FIELD_RULES = [
        'category_id'              => ['integer'],
        'type_id'                  => ['integer'],
        'name'                     => ['string', 'max:255'],
        'resolution_id'            => ['integer'],
        'region_id'                => ['integer'],
        'distributor_id'           => ['integer'],
        'season_number'            => ['integer', 'min:0', 'max:9999'],
        'episode_number'           => ['integer', 'min:0', 'max:9999'],
        'imdb'                     => ['string', 'max:32'],
        'tvdb'                     => ['string', 'max:32'],
        'mal'                      => ['string', 'max:32'],
        'igdb'                     => ['string', 'max:32'],
        'tmdb_movie_id'            => ['string', 'max:32'],
        'tmdb_tv_id'               => ['string', 'max:32'],
        'musicbrainz_id'           => ['string', 'max:64'],
        'open_library_edition_id'  => ['string', 'max:32'],
        'movie_exists_on_tmdb'     => ['boolean'],
        'tv_exists_on_tmdb'        => ['boolean'],
        'title_exists_on_imdb'     => ['boolean'],
        'tv_exists_on_tvdb'        => ['boolean'],
        'anime_exists_on_mal'      => ['boolean'],
        'game_exists_on_igdb'      => ['boolean'],
        'edition_kind'             => ['string', 'max:32'],
        'edition_name'             => ['string', 'max:255'],
        'edition_provenance'       => ['string', 'max:2000'],
        'keywords'                 => ['string', 'max:255'],
        'description'              => ['string', 'max:65535'],
        'mediainfo'                => ['string', 'max:65535'],
        'bdinfo'                   => ['string', 'max:2097152'],
        'anon'                     => ['boolean'],
        'personal_release'         => ['boolean'],
        'mod_queue_opt_in'         => ['boolean'],
        'internal'                 => ['boolean'],
        'refundable'               => ['boolean'],
        'free'                     => ['integer', 'between:0,100'],
        // media_work_id is kept (a restored selection still publishes as a
        // quality variant of that Work), but metadata_selection_token is
        // deliberately NOT part of this allowlist: a restored draft must
        // never silently reinstate a provider metadata snapshot as "current"
        // without the uploader re-confirming it this session.
        'media_work_id'            => ['integer'],
    ];

    /**
     * List the authenticated user's own drafts (private, owner-only).
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user->can_upload ?? $user->group->can_upload, 403, __('torrent.cant-upload').' '.__('torrent.cant-upload-desc'));

        $drafts = UploadDraft::query()
            ->where('user_id', '=', $user->id)
            ->orderByDesc('updated_at')
            ->get(['id', 'title', 'updated_at']);

        return response()->json([
            'drafts' => $drafts->map(static fn (UploadDraft $draft): array => [
                'id'         => $draft->id,
                'title'      => $draft->title,
                'updated_at' => $draft->updated_at?->toIso8601String(),
            ])->values(),
        ]);
    }

    /**
     * Create a new draft or overwrite one of the authenticated user's own
     * existing drafts (an `id` belonging to another user is rejected as
     * not found, never overwritten).
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user->can_upload ?? $user->group->can_upload, 403, __('torrent.cant-upload').' '.__('torrent.cant-upload-desc'));

        $validated = $request->validate([
            'id'     => ['nullable', 'integer'],
            'title'  => ['nullable', 'string', 'max:191'],
            'fields' => ['present', 'array'],
        ]);

        $allowedInput = array_intersect_key($validated['fields'], self::FIELD_RULES);

        $fieldValidator = Validator::make(
            $allowedInput,
            array_intersect_key(self::FIELD_RULES, $allowedInput),
        );
        $fieldValidator->validate();
        $fields = $fieldValidator->validated();

        $draft = null;

        if (!empty($validated['id'])) {
            $draft = UploadDraft::query()->where('user_id', '=', $user->id)->find($validated['id']);
            abort_if($draft === null, 404);
        }

        if ($draft === null) {
            $draft = new UploadDraft(['user_id' => $user->id]);
        }

        $draft->title = $validated['title'] ?? $draft->title;
        $draft->fields = $fields;
        $draft->save();

        return response()->json(['draft' => $this->present($draft)]);
    }

    /**
     * Read one of the authenticated user's own drafts in full (404 for
     * drafts that don't exist or belong to someone else).
     */
    public function show(Request $request, UploadDraft $draft): JsonResponse
    {
        $user = $request->user();
        abort_unless($user->can_upload ?? $user->group->can_upload, 403, __('torrent.cant-upload').' '.__('torrent.cant-upload-desc'));
        abort_unless($draft->user_id === $user->id, 404);

        return response()->json(['draft' => $this->present($draft)]);
    }

    /**
     * Delete one of the authenticated user's own drafts (404 for drafts
     * that don't exist or belong to someone else).
     */
    public function destroy(Request $request, UploadDraft $draft): JsonResponse
    {
        $user = $request->user();
        abort_unless($user->can_upload ?? $user->group->can_upload, 403, __('torrent.cant-upload').' '.__('torrent.cant-upload-desc'));
        abort_unless($draft->user_id === $user->id, 404);

        $draft->delete();

        return response()->json(['deleted' => true]);
    }

    /**
     * @return array{id: int, title: ?string, fields: array<string, mixed>, updated_at: ?string}
     */
    private function present(UploadDraft $draft): array
    {
        return [
            'id'         => $draft->id,
            'title'      => $draft->title,
            'fields'     => $draft->fields,
            'updated_at' => $draft->updated_at?->toIso8601String(),
        ];
    }
}
