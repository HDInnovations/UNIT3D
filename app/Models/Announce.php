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
 * @author     HDVinnie <hdinnovations@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Announce.
 *
 * @property int    $id
 * @property int    $user_id
 * @property int    $torrent_id
 * @property int    $uploaded
 * @property int    $downloaded
 * @property int    $left
 * @property int    $corrupt
 * @property mixed  $peer_id
 * @property int    $port
 * @property int    $numwant
 * @property string $created_at
 * @property string $event
 * @property string $key
 */
class Announce extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]
     */
    protected $guarded = ['id'];

    /**
     * Get the torrent that was announced.
     *
     * @return BelongsTo<Torrent, $this>
     */
    public function torrents(): BelongsTo
    {
        return $this->belongsTo(Torrent::class);
    }

    /**
     * Get the user that announced.
     *
     * @return BelongsTo<User, $this>
     */
    public function requests(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
