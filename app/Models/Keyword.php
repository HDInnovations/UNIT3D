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
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\Keyword.
 *
 * @property int         $id
 * @property string      $name
 * @property int         $torrent_id
 * @property string|null $created_at
 * @property string|null $updated_at
 */
#[WithoutTimestamps]
#[Fillable('name')]
#[AllowDynamicProperties]
final class Keyword extends Model
{
    /** @use HasFactory<\Database\Factories\KeywordFactory> */
    use HasFactory;

    /**
     * Get the torrents that have this keyword.
     *
     * @return BelongsToMany<Torrent, $this>
     */
    public function torrents(): BelongsToMany
    {
        return $this->belongsToMany(Torrent::class);
    }
}
