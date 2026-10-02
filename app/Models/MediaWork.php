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

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\MediaWork.
 *
 * A canonical, category-agnostic catalogue entry grouping every Torrent
 * (across editions/variants/qualities) that represents the same abstract
 * Work (see CONTEXT.md: Work -> Edition -> Variant -> Torrent). Identity is
 * derived from a trusted provider id (TMDB movie/tv, IGDB, MusicBrainz
 * release-group, Open Library work/edition) or, for provider-less content,
 * a unique provisional identity scoped to the uploading torrent so unrelated
 * releases are never silently merged by filename alone.
 *
 * @property int                             $id
 * @property string                          $identity_key
 * @property string                          $kind
 * @property string|null                     $source
 * @property string|null                     $source_id
 * @property string                          $title
 * @property string|null                     $description
 * @property string|null                     $cover_url
 * @property string|null                     $source_url
 * @property array<string, mixed>|null       $raw
 * @property array<string, mixed>|null       $facets
 * @property \Illuminate\Support\Carbon|null $metadata_updated_at
 * @property string|null                     $metadata_error
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class MediaWork extends Model
{
    protected $guarded = [];

    /**
     * @return array{raw: 'array', facets: 'array', metadata_updated_at: 'datetime'}
     */
    protected function casts(): array
    {
        return [
            'raw'                  => 'array',
            'facets'               => 'array',
            'metadata_updated_at'  => 'datetime',
        ];
    }

    /**
     * Get every torrent (every edition/variant/quality) grouped under this Work.
     *
     * @return HasMany<Torrent, $this>
     */
    public function torrents(): HasMany
    {
        return $this->hasMany(Torrent::class);
    }

    /**
     * Best-effort release year derived from the raw provider payload, for
     * catalogue display (list/card/group/poster and Work search results).
     * Provider-less/provisional Works simply have no year.
     */
    public function displayYear(): ?string
    {
        $raw = $this->raw ?? [];

        $date = match ($this->kind) {
            'movie' => $raw['release_date'] ?? null,
            'tv'    => $raw['first_air_date'] ?? null,
            'music' => $raw['release-group']['first-release-date'] ?? $raw['first-release-date'] ?? $raw['date'] ?? null,
            'game'  => \is_int($raw['first_release_date'] ?? null) ? date('Y-m-d', $raw['first_release_date']) : null,
            default => null,
        };

        return \is_string($date) && preg_match('/^(\d{4})/', $date, $matches) === 1 ? $matches[1] : null;
    }
}
