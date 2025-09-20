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

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Report.
 *
 * @property int                             $id
 * @property string                          $type
 * @property int                             $reporter_id
 * @property int|null                        $staff_id
 * @property string                          $title
 * @property string                          $message
 * @property bool                            $solved
 * @property string|null                     $verdict
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null                        $reported_user
 * @property int|null                        $torrent_id
 * @property int|null                        $request_id
 * @property \Illuminate\Support\Carbon|null $snoozed_until
 */
class Report extends Model
{
    use Auditable;

    /** @use HasFactory<\Database\Factories\ReportFactory> */
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array{solved: 'bool', snoozed_until: 'datetime'}
     */
    protected function casts(): array
    {
        return [
            'solved'        => 'bool',
            'snoozed_until' => 'datetime',
        ];
    }

    /**
     * Get the request that was reported.
     *
     * @return BelongsTo<TorrentRequest, $this>
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(TorrentRequest::class, 'request_id');
    }

    /**
     * Get the torrent that was reported.
     *
     * @return BelongsTo<Torrent, $this>
     */
    public function torrent(): BelongsTo
    {
        return $this->belongsTo(Torrent::class, 'torrent_id');
    }

    /**
     * Get the user that reported.
     *
     * @return BelongsTo<User, $this>
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id')->withTrashed();
    }

    /**
     * Get the user that was reported.
     *
     * @return BelongsTo<User, $this>
     */
    public function reported(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_user')->withTrashed();
    }

    /**
     * Get the staff user that solved the report.
     *
     * @return BelongsTo<User, $this>
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id')->withTrashed();
    }
}
