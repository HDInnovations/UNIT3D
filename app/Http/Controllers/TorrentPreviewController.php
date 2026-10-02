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

use App\Helpers\Bencode;
use App\Helpers\TorrentTools;
use App\Helpers\UploadKinds;
use App\Http\Requests\StoreTorrentRequest;
use App\Models\Category;
use App\Models\Distributor;
use App\Models\MediaWork;
use App\Models\Region;
use App\Models\Resolution;
use App\Models\Type;
use App\Services\Metadata\MetadataSelectionStore;
use Illuminate\Http\JsonResponse;

/**
 * Read-only, side-effect-free preview of what a torrent would look like if
 * published right now. Runs the exact same validation as the real upload
 * (StoreTorrentRequest, including the uploaded .torrent file), reads the
 * normalized metainfo, and renders a safe preview fragment. Never writes to
 * storage, never creates a Torrent, never dispatches jobs/announces/
 * notifications. The real <form> POST to torrents.store remains the only
 * way to actually publish.
 */
class TorrentPreviewController extends Controller
{
    public function __construct(private readonly MetadataSelectionStore $selectionStore)
    {
    }

    public function store(StoreTorrentRequest $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user->can_upload ?? $user->group->can_upload, 403, __('torrent.cant-upload').' '.__('torrent.cant-upload-desc'));

        abort_if(\is_array($request->file('torrent')), 400);

        $category = Category::findOrFail($request->integer('category_id'));
        $type = Type::findOrFail($request->integer('type_id'));
        $kind = UploadKinds::categoryKind($category);

        $decodedTorrent = TorrentTools::normalizeTorrent($request->file('torrent'));
        $meta = Bencode::get_meta($decodedTorrent);
        $folderName = Bencode::get_name($decodedTorrent);

        $resolution = $request->filled('resolution_id')
            ? Resolution::find($request->integer('resolution_id'))
            : null;
        $region = $request->filled('region_id')
            ? Region::find($request->integer('region_id'))
            : null;
        $distributor = $request->filled('distributor_id')
            ? Distributor::find($request->integer('distributor_id'))
            : null;

        $scope = match (true) {
            $kind !== 'tv'                                                                      => null,
            $request->integer('season_number') === 0 && $request->integer('episode_number') === 0 => __('upload-flow.preview.scope-complete'),
            $request->integer('episode_number') === 0                                             => __('upload-flow.preview.scope-season', ['season' => $request->integer('season_number')]),
            default                                                                               => __('upload-flow.preview.scope-episode', [
                'season'  => $request->integer('season_number'),
                'episode' => $request->integer('episode_number'),
            ]),
        };

        $coverUrl = null;

        $selectionToken = $request->filled('metadata_selection_token')
            ? $request->string('metadata_selection_token')->toString()
            : null;

        if ($selectionToken !== null) {
            // Throws a ValidationException (surfaced as a normal 422 with the
            // usual field error) when the token is missing/expired/mismatched.
            $lookup = $this->selectionStore->retrieve($user, $category, $selectionToken);
            $coverUrl = $lookup['cover_url'] ?? null;
        }

        if ($request->filled('media_work_id')) {
            // StoreTorrentRequest (shared by torrents.store and this preview
            // endpoint) already validated the Work's existence, kind, and
            // visibility before this controller method runs; this is purely
            // a read for display, not a second authorization gate.
            $work = MediaWork::query()->find($request->integer('media_work_id'));

            $coverUrl ??= $work?->cover_url;
        }

        $html = view('torrent.preview', [
            'title'        => $request->string('name')->toString(),
            'folderName'   => $folderName,
            'description'  => $request->string('description')->toString(),
            'category'     => $category,
            'type'         => $type,
            'resolution'   => $resolution,
            'region'       => $region,
            'distributor'  => $distributor,
            'scope'        => $scope,
            'size'         => (int) $meta['size'],
            'count'        => (int) $meta['count'],
            'coverUrl'     => $coverUrl,
            'anon'         => $request->boolean('anon'),
            'personal'     => $request->boolean('personal_release'),
            'modQueueOptIn' => $request->boolean('mod_queue_opt_in'),
            'keywords'     => $request->filled('keywords') ? TorrentTools::parseKeywords($request->string('keywords')) : [],
        ])->render();

        return response()->json(['html' => $html]);
    }
}
