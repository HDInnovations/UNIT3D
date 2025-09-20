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

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Peer.
 *
 * @property int                             $id
 * @property mixed                           $peer_id
 * @property mixed                           $ip
 * @property int                             $port
 * @property string                          $agent
 * @property int                             $uploaded
 * @property int                             $downloaded
 * @property int                             $left
 * @property bool                            $seeder
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int                             $torrent_id
 * @property int                             $user_id
 * @property bool                            $connectable
 * @property bool                            $active
 */
class Peer extends Model
{
    /** @use HasFactory<\Database\Factories\PeerFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array{active: 'bool', seeder: 'bool', connectable: 'bool'}
     */
    protected function casts(): array
    {
        return [
            'active'      => 'bool',
            'seeder'      => 'bool',
            'connectable' => 'bool',
        ];
    }

    /**
     * Prepare a date for array / JSON serialization.
     */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Get the user associated with the peer.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault([
            'username' => 'System',
            'id'       => User::SYSTEM_USER_ID,
        ]);
    }

    /**
     * Get the torrent associated with the peer.
     *
     * @return BelongsTo<Torrent, $this>
     */
    public function torrent(): BelongsTo
    {
        return $this->belongsTo(Torrent::class);
    }

    /**
     * Get the torrent associated with the peer.
     *
     * @return BelongsTo<Torrent, $this>
     */
    public function seed(): BelongsTo
    {
        return $this->belongsTo(Torrent::class, 'torrents.id', 'torrent_id');
    }
}
