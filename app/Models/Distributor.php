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
 * App\Models\Distributor.
 *
 * @property int    $id
 * @property string $name
 */
#[WithoutTimestamps]
#[Guarded('id', 'created_at', 'updated_at')]
#[AllowDynamicProperties]
final class Distributor extends Model
{
    use Auditable;

    /** @use HasFactory<\Database\Factories\DistributorFactory> */
    use HasFactory;

    /**
     * Get the torrents for this distributor.
     *
     * @return HasMany<Torrent, $this>
     */
    public function torrents(): HasMany
    {
        return $this->hasMany(Torrent::class);
    }
}
