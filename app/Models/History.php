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

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\History.
 *
 * @property int                             $user_id
 * @property int                             $torrent_id
 * @property string                          $agent
 * @property int                             $uploaded
 * @property int                             $actual_uploaded
 * @property int                             $client_uploaded
 * @property int                             $downloaded
 * @property int                             $refunded_download
 * @property int                             $actual_downloaded
 * @property int                             $client_downloaded
 * @property int                             $seeder
 * @property int                             $active
 * @property int                             $seedtime
 * @property int                             $immune
 * @property bool                            $hitrun
 * @property \Illuminate\Support\Carbon|null $prewarned_at
 */
class History extends Model
{
    /** @use HasFactory<\Database\Factories\HistoryFactory> */
    use HasFactory;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'history';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array{completed_at: 'datetime', hitrun: 'bool', prewarned_at: 'datetime'}
     */
    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
            'hitrun'       => 'bool',
            'prewarned_at' => 'datetime',
        ];
    }

    /**
     * Get the user associated with the history.
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
     * Get the torrent associated with the history.
     *
     * @return BelongsTo<Torrent, $this>
     */
    public function torrent(): BelongsTo
    {
        return $this->belongsTo(Torrent::class);
    }

    /**
     * Prepare a date for array / JSON serialization.
     */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
