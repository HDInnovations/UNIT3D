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

use App\Helpers\UploadKinds;
use App\Models\Category;
use App\Models\MediaWork;
use App\Models\Torrent;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Renders the canonical, category-agnostic catalogue Work page (one title,
 * every quality/edition/scope variant) and powers the upload form's
 * "attach to an existing title" search.
 */
class MediaWorkController extends Controller
{
    /**
     * Common Work metadata rendered once, with every accessible variant
     * (quality/edition/season/episode/package) separately paginated below
     * it so a title with many releases never truncates its variant list.
     */
    public function show(Request $request, MediaWork $work): View
    {
        $user = $request->user();

        // A Work with zero currently-accessible variants (every torrent
        // hidden/pending/rejected/soft-deleted) must not leak its shared
        // metadata to the catalogue; matches the default Torrent scopes
        // (approved + non-deleted) applied everywhere else in the catalogue.
        abort_unless($work->torrents()->exists(), 404);

        $variants = Torrent::query()
            ->where('media_work_id', '=', $work->id)
            ->with([
                'user:id,username,group_id',
                'user.group',
                'category:id,name,position',
                'type:id,name,position',
                'resolution:id,name,position',
            ])
            ->withCount(['comments'])
            ->when(
                !config('announce.external_tracker.is_enabled'),
                fn ($query) => $query->withCount([
                    'seeds'   => fn ($query) => $query->where('active', '=', true)->where('visible', '=', true),
                    'leeches' => fn ($query) => $query->where('active', '=', true)->where('visible', '=', true),
                ]),
            )
            ->withExists([
                'featured as featured',
                'bookmarks'          => fn ($query) => $query->where('user_id', '=', $user->id),
                'freeleechTokens'    => fn ($query) => $query->where('user_id', '=', $user->id),
                'history as seeding' => fn ($query) => $query->where('user_id', '=', $user->id)
                    ->where('active', '=', 1)
                    ->where('seeder', '=', 1),
                'history as leeching' => fn ($query) => $query->where('user_id', '=', $user->id)
                    ->where('active', '=', 1)
                    ->where('seeder', '=', 0),
                'history as completed' => fn ($query) => $query->where('user_id', '=', $user->id)
                    ->where('active', '=', 0)
                    ->where('seeder', '=', 1),
                'trump',
            ])
            ->orderByDesc('sticky')
            ->orderBy('season_number')
            ->orderBy('episode_number')
            ->orderByDesc('bumped_at')
            ->paginate(25)
            ->withQueryString();

        return view('work.show', [
            'work'     => $work,
            'variants' => $variants,
        ]);
    }

    /**
     * Bounded existing-title search for the upload form's "add a quality
     * variant to this title" selector. Only returns Works of the submitted
     * category's kind that currently have at least one torrent accessible
     * under the catalogue's normal privacy/moderation rules.
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'query'       => ['required', 'string', 'min:2', 'max:200'],
        ]);

        $category = Category::query()->findOrFail($validated['category_id']);
        $kind = UploadKinds::categoryKind($category);

        $works = MediaWork::query()
            ->where('kind', '=', $kind)
            ->where('title', 'LIKE', '%'.str_replace(' ', '%', trim($validated['query'])).'%')
            ->whereHas('torrents')
            ->orderBy('title')
            ->limit(20)
            ->get();

        return response()->json([
            'results' => $works->map(fn (MediaWork $work): array => [
                'id'        => $work->id,
                'title'     => $work->title,
                'kind'      => $work->kind,
                'source'    => $work->source,
                'source_id' => $work->source_id,
                'cover_url' => $work->cover_url,
                'year'      => $work->displayYear(),
            ])->values(),
        ]);
    }
}
