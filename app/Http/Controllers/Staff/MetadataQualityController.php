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

namespace App\Http\Controllers\Staff;

use App\Exceptions\InvalidMetadataIdentifierException;
use App\Exceptions\MetadataNotFoundException;
use App\Exceptions\MetadataProviderUnavailableException;
use App\Helpers\UploadKinds;
use App\Http\Controllers\Controller;
use App\Models\MediaWork;
use App\Models\Scopes\ApprovedScope;
use App\Models\Torrent;
use App\Services\Media\MediaWorkCatalog;
use App\Services\Metadata\MetadataDetails;
use App\Services\Metadata\TorrentMetadataLookup;
use App\Services\Staff\MetadataQualityPreviewStore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Staff-only dashboard for reviewing shared catalogue Work metadata quality
 * (missing raw/cover/description, recorded provider errors) and running a
 * targeted, explicitly reviewed and confirmed refresh of a single Work's
 * metadata against its own provider source.
 *
 * Never mutates a Work's shared metadata without an explicit, field-level
 * staff confirmation of a diff that was fetched fresh for this exact
 * review; never touches per-torrent names/descriptions.
 *
 * @phpstan-import-type LookupResult from TorrentMetadataLookup
 */
final class MetadataQualityController extends Controller
{
    /**
     * Upload kinds a provider can actually be asked to refresh. "no" and
     * "xxx" Works are provisional/non-provider identities by design; a
     * missing cover/description/raw there is not a quality problem.
     *
     * @var list<string>
     */
    private const array REFRESHABLE_KINDS = ['movie', 'tv', 'game', 'music', 'book'];

    /**
     * @var list<string>
     */
    private const array APPLICABLE_FIELDS = ['title', 'description', 'cover_url', 'raw'];

    public function __construct(
        private readonly TorrentMetadataLookup $lookup,
        private readonly MetadataQualityPreviewStore $store,
        private readonly MediaWorkCatalog $catalog,
    ) {
    }

    /**
     * Paginated, searchable/filterable dashboard of provider-backed Works,
     * surfacing missing raw/cover/description and recorded provider errors.
     * A field that is legitimately never provided by its source (for
     * example a MusicBrainz release has no prose description) is never
     * reported as missing.
     */
    public function index(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $this->authorizeReviewer($request);

        $validated = $request->validate([
            'kind'   => ['nullable', 'string', Rule::in(self::REFRESHABLE_KINDS)],
            'title'  => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', Rule::in(['all', 'missing_raw', 'missing_cover', 'missing_description', 'error'])],
        ]);

        $status = $validated['status'] ?? 'all';

        $works = MediaWork::query()
            ->whereNotNull('source')
            ->whereNotNull('source_id')
            ->when($validated['kind'] ?? null, fn ($query, $kind) => $query->where('kind', $kind))
            ->when($validated['title'] ?? null, fn ($query, $title) => $query->where('title', 'like', '%'.addcslashes($title, '%_\\').'%'))
            ->when($status === 'missing_raw', fn ($query) => $query->where(fn ($query) => $query->whereNull('raw')->orWhereJsonLength('raw', 0)))
            ->when($status === 'missing_cover', fn ($query) => $query->whereNull('cover_url'))
            ->when(
                $status === 'missing_description',
                // Music descriptions are expected to be empty; they are never a quality issue.
                fn ($query) => $query->where('kind', '!=', 'music')
                    ->where(fn ($query) => $query->whereNull('description')->orWhere('description', ''))
            )
            ->when($status === 'error', fn ($query) => $query->whereNotNull('metadata_error'))
            ->orderByDesc('metadata_error')
            ->orderBy('title')
            ->paginate(25)
            ->withQueryString();

        return view('Staff.metadata-quality.index', [
            'works'  => $works,
            'kind'   => $validated['kind'] ?? '',
            'title'  => $validated['title'] ?? '',
            'status' => $status,
            'kinds'  => self::REFRESHABLE_KINDS,
        ]);
    }

    /**
     * Fetches a fresh provider lookup for this Work (using the source/
     * source_id of one of its own compatible torrents) and renders a
     * read-only, field-level diff against the currently stored metadata.
     * Never persists title/description/cover/raw itself; on a provider
     * failure it only records the failure reason (never erasing existing
     * metadata) so the dashboard can distinguish an outage from a genuine
     * not-found.
     */
    public function preview(Request $request, MediaWork $work): \Illuminate\Contracts\View\Factory|\Illuminate\View\View|RedirectResponse
    {
        $this->authorizeReviewer($request);
        $this->guardRefreshable($work);

        $torrent = $this->representativeTorrent($work);

        if ($torrent === null) {
            return back()->withErrors([
                'metadata_quality' => __('metadata-quality.errors.no-compatible-torrent'),
            ]);
        }

        try {
            $identifier = $work->kind === 'book' && $torrent->metadata?->source === 'open-library'
                ? $torrent->metadata->source_id
                : $work->source_id;

            if (!filled($identifier)) {
                return back()->withErrors(['metadata_quality' => __('metadata-quality.errors.no-compatible-torrent')]);
            }

            $lookup = $this->lookup->lookup($torrent->category, (string) $identifier);
        } catch (InvalidMetadataIdentifierException $exception) {
            $work->updateQuietly(['metadata_error' => __('metadata-quality.errors.source-unsupported')]);

            return back()->withErrors(['metadata_quality' => $exception->getMessage()]);
        } catch (MetadataNotFoundException $exception) {
            $work->updateQuietly(['metadata_error' => __('metadata-quality.errors.source-not-found')]);

            return back()->withErrors(['metadata_quality' => $exception->getMessage()]);
        } catch (MetadataProviderUnavailableException $exception) {
            $work->updateQuietly(['metadata_error' => __('metadata-quality.errors.source-unavailable')]);

            return back()->withErrors(['metadata_quality' => $exception->getMessage()]);
        }

        $diff = $this->buildDiff($work, $lookup);
        $token = $this->store->remember($request->user(), $work, $lookup);

        return view('Staff.metadata-quality.preview', [
            'work'  => $work,
            'diff'  => $diff,
            'token' => $token,
        ]);
    }

    /**
     * Applies the explicitly selected fields from a previously reviewed,
     * still-fresh preview. Rejects (422, via `preview_token` validation
     * error) a token that is missing/expired, belongs to a different
     * user/Work, or whose Work has changed since the preview was taken.
     */
    public function update(Request $request, MediaWork $work): RedirectResponse
    {
        $this->authorizeReviewer($request);
        $this->guardRefreshable($work);

        $validated = $request->validate([
            'preview_token' => ['required', 'string'],
            'fields'        => ['required', 'array', 'min:1'],
            'fields.*'      => [Rule::in(self::APPLICABLE_FIELDS)],
        ]);

        try {
            $work = DB::transaction(function () use ($request, $work, $validated): MediaWork {
                $current = MediaWork::query()->whereKey($work->id)->lockForUpdate()->firstOrFail();
                $this->guardRefreshable($current);
                $snapshot = $this->store->retrieve($request->user(), $current, $validated['preview_token']);

                return $this->catalog->apply($current, $snapshot['lookup'], $validated['fields']);
            });
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors());
        }

        return redirect()
            ->route('staff.metadata-quality.index')
            ->with('success', __('metadata-quality.messages.applied', ['title' => $work->title]));
    }

    private function authorizeReviewer(Request $request): void
    {
        $group = $request->user()->group;

        abort_unless($group->is_modo || $group->is_editor || $group->is_torrent_modo, 403);
    }

    private function guardRefreshable(MediaWork $work): void
    {
        abort_unless(
            \in_array($work->kind, self::REFRESHABLE_KINDS, true)
                && filled($work->source)
                && filled($work->source_id),
            422,
            __('metadata-quality.errors.no-refreshable-source'),
        );
    }

    /**
     * One non-deleted torrent already linked to this Work whose category
     * kind matches the Work's own kind, used only to read its category
     * (and therefore which provider to query) — never its name/description.
     */
    private function representativeTorrent(MediaWork $work): ?Torrent
    {
        return Torrent::withoutGlobalScope(ApprovedScope::class)
            ->where('media_work_id', $work->id)
            ->whereNotNull('category_id')
            ->with(['category', 'metadata'])
            ->limit(50)
            ->get()
            ->first(fn (Torrent $torrent): bool => $torrent->category !== null
                && UploadKinds::categoryKind($torrent->category) === $work->kind);
    }

    /**
     * @param LookupResult $lookup
     *
     * @return array<string, array{old: mixed, new: mixed, changed: bool, expected_empty?: bool}>
     */
    private function buildDiff(MediaWork $work, array $lookup): array
    {
        $oldRaw = \is_array($work->raw) ? $work->raw : [];
        $newRaw = \is_array($lookup['raw'] ?? null) ? $lookup['raw'] : [];

        return [
            'title' => [
                'old'     => $work->title,
                'new'     => $lookup['title'],
                'changed' => $work->title !== $lookup['title'],
            ],
            'description' => [
                'old'            => $work->description,
                'new'            => $lookup['description'],
                'changed'        => (string) $work->description !== (string) $lookup['description'],
                // Music has no provider prose; an empty value there is expected, not a corruption.
                'expected_empty' => $work->kind === 'music',
            ],
            'cover_url' => [
                'old'     => $work->cover_url,
                'new'     => $lookup['cover_url'],
                'changed' => $work->cover_url !== $lookup['cover_url'],
            ],
            'raw' => [
                'old'     => MetadataDetails::forSource((string) ($work->source ?? $lookup['source']), $oldRaw),
                'new'     => MetadataDetails::forSource($lookup['source'], $newRaw),
                'changed' => $oldRaw != $newRaw, // phpcs:ignore -- deliberate loose compare, key order is irrelevant
            ],
        ];
    }
}
