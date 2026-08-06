<?php

declare(strict_types=1);

/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D
 *
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 * @author     HDVinnie
 */

namespace App\Models;

use AllowDynamicProperties;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Resolution.
 *
 * @property int    $id
 * @property string $name
 * @property int    $position
 */
#[WithoutTimestamps]
#[Guarded('id', 'created_at', 'updated_at')]
#[AllowDynamicProperties]
final class Resolution extends Model
{
    use Auditable;

    /** @use HasFactory<\Database\Factories\ResolutionFactory> */
    use HasFactory;

    /**
     * Get all torrents for the resolution.
     *
     * @return HasMany<Torrent, $this>
     */
    public function torrents(): HasMany
    {
        return $this->hasMany(Torrent::class);
    }

    /**
     * Get all requests for the resolution.
     *
     * @return HasMany<TorrentRequest, $this>
     */
    public function requests(): HasMany
    {
        return $this->hasMany(TorrentRequest::class);
    }
}
