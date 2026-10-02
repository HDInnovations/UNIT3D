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
use App\Exceptions\MetadataNotFoundException;
use App\Exceptions\MetadataProviderUnavailableException;
use App\Models\Category;
use App\Services\Metadata\MetadataSelectionStore;
use App\Services\Metadata\TorrentMetadataLookup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TorrentMetadataController extends Controller
{
    /**
     * Live, synchronous, read-only pre-upload metadata lookup for the
     * torrent create form's "fetch metadata" button. Never persists
     * anything and never dispatches jobs.
     */
    public function show(Request $request, TorrentMetadataLookup $lookup, MetadataSelectionStore $selectionStore): JsonResponse
    {
        $user = $request->user();
        abort_unless($user->can_upload ?? $user->group->can_upload, 403, __('torrent.cant-upload').' '.__('torrent.cant-upload-desc'));

        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'identifier'  => ['required', 'string', 'max:64'],
        ]);

        $category = Category::query()->findOrFail($validated['category_id']);

        try {
            $result = $lookup->lookup($category, $validated['identifier']);
        } catch (InvalidMetadataIdentifierException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        } catch (MetadataNotFoundException $exception) {
            return response()->json(['message' => $exception->getMessage()], 404);
        } catch (MetadataProviderUnavailableException $exception) {
            return response()->json(['message' => $exception->getMessage()], 503);
        }

        // The trusted, server-fetched snapshot is cached under an unguessable
        // token so publication can later confirm the exact provider payload
        // the uploader was shown, without trusting anything client-supplied.
        $result['selection_token'] = $selectionStore->remember($user, $category, $result);

        return response()->json($result);
    }
}
