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

namespace App\Services\Staff;

use App\Models\MediaWork;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Caches a single staff metadata-refresh preview (the freshly fetched
 * provider lookup for one Work) server-side, bound to the reviewing staff
 * member and the specific Work, behind an unguessable one-time token.
 *
 * The confirmation step ({@see retrieve()}) re-derives a fingerprint of the
 * Work's current stored metadata and rejects the token if it no longer
 * matches the fingerprint captured at preview time (the Work changed, was
 * refreshed again, or was refreshed by someone else in the meantime), or if
 * the token belongs to a different user/Work/kind. This is the "stale
 * confirm" guard required before {@see \App\Services\Media\MediaWorkCatalog::apply()}
 * is ever called.
 *
 * @phpstan-import-type LookupResult from \App\Services\Metadata\TorrentMetadataLookup
 */
final class MetadataQualityPreviewStore
{
    private const int TTL_MINUTES = 20;

    /**
     * Cache the reviewed lookup for this user/Work pair and return an
     * unguessable confirmation token.
     *
     * @param LookupResult $lookup
     */
    public function remember(User $user, MediaWork $work, array $lookup): string
    {
        $token = (string) Str::uuid();

        Cache::put(self::key($token), [
            'user_id'      => $user->id,
            'work_id'      => $work->id,
            'kind'         => $work->kind,
            'identity_key' => $work->identity_key,
            'fingerprint'  => $this->fingerprint($work),
            'lookup'       => $lookup,
        ], now()->addMinutes(self::TTL_MINUTES));

        return $token;
    }

    /**
     * Validate and consume a preview token, returning the cached lookup.
     *
     * Rejects (with a localized ValidationException on `preview_token`)
     * when the token is unknown/expired, belongs to a different user or a
     * different Work, or when the Work's identity/kind/current metadata no
     * longer matches the snapshot captured at preview time.
     *
     * The token is single-use: once retrieved (whether or not the caller
     * goes on to apply it), it is forgotten so the same reviewed diff can
     * never be replayed.
     *
     * @return array{lookup: LookupResult}
     */
    public function retrieve(User $user, MediaWork $work, string $token): array
    {
        $snapshot = Cache::get(self::key($token));

        $valid = \is_array($snapshot)
            && ($snapshot['user_id'] ?? null) === $user->id
            && ($snapshot['work_id'] ?? null) === $work->id
            && ($snapshot['kind'] ?? null) === $work->kind
            && ($snapshot['identity_key'] ?? null) === $work->identity_key
            && \is_array($snapshot['lookup'] ?? null)
            && ($snapshot['fingerprint'] ?? null) === $this->fingerprint($work);

        if (!$valid) {
            // Deliberately does not forget the cache entry here: a mismatched
            // attempt (wrong user/Work, or a race with another request) must
            // never burn a token that is still legitimately redeemable by its
            // rightful owner.
            throw ValidationException::withMessages([
                'preview_token' => __('metadata-quality.errors.preview-stale'),
            ]);
        }

        Cache::forget(self::key($token));

        /** @var array{lookup: LookupResult} $snapshot */
        return ['lookup' => $snapshot['lookup']];
    }

    /**
     * A fingerprint of everything on the Work that a refresh could change,
     * plus its last-touched timestamp. Any concurrent change invalidates
     * outstanding preview tokens.
     */
    public function fingerprint(MediaWork $work): string
    {
        return hash('sha256', (string) json_encode([
            $work->title,
            $work->description,
            $work->cover_url,
            $work->source,
            $work->source_id,
            $work->source_url,
            $work->facets,
            $work->raw,
            $work->metadata_error,
            $work->metadata_updated_at?->timestamp,
            $work->updated_at?->timestamp,
        ]));
    }

    private static function key(string $token): string
    {
        return 'metadata-quality-preview:'.$token;
    }
}
