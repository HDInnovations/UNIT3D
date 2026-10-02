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

use App\Exceptions\InvalidMetadataIdentifierException;
use App\Exceptions\MetadataProviderUnavailableException;
use App\Helpers\UploadKinds;
use App\Models\Category;
use App\Services\Metadata\TorrentMetadataSearch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TorrentMetadataSearchController extends Controller
{
    /**
     * Bounded, live provider title search for the upload form's explicit
     * "search" action. Never selects, loads, or persists a result; it only
     * returns candidates for the uploader to pick from.
     */
    public function index(Request $request, TorrentMetadataSearch $search): JsonResponse
    {
        $this->authorizeUploader($request);

        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'query'       => ['required', 'string', 'min:2', 'max:200'],
        ]);

        $category = Category::query()->findOrFail($validated['category_id']);

        try {
            $results = $search->search($category, $validated['query']);
        } catch (InvalidMetadataIdentifierException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        } catch (MetadataProviderUnavailableException $exception) {
            return response()->json(['message' => $exception->getMessage()], 503);
        }

        return response()->json(['results' => $results]);
    }

    /**
     * Bounded, paginated MusicBrainz releases (editions) belonging to
     * exactly one, explicitly chosen release-group. Never picks a release.
     */
    public function musicReleases(Request $request, TorrentMetadataSearch $search): JsonResponse
    {
        $this->authorizeUploader($request);

        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'identifier'  => ['required', 'string', 'max:64'],
            'offset'      => ['nullable', 'integer', 'min:0'],
        ]);

        $category = Category::query()->findOrFail($validated['category_id']);

        if (UploadKinds::categoryKind($category) !== 'music') {
            return response()->json(['message' => __('discovery.errors.category-not-searchable')], 422);
        }

        try {
            $page = $search->musicReleases($validated['identifier'], (int) ($validated['offset'] ?? 0));
        } catch (InvalidMetadataIdentifierException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        } catch (MetadataProviderUnavailableException $exception) {
            return response()->json(['message' => $exception->getMessage()], 503);
        }

        return response()->json($page);
    }

    private function authorizeUploader(Request $request): void
    {
        $user = $request->user();

        abort_unless($user->can_upload ?? $user->group->can_upload, 403, __('torrent.cant-upload').' '.__('torrent.cant-upload-desc'));
    }
}
