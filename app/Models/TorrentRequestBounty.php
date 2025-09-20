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
 * App\Models\TorrentRequestBounty.
 *
 * @property int                             $id
 * @property int                             $user_id
 * @property string                          $seedbonus
 * @property int                             $requests_id
 * @property bool                            $anon
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class TorrentRequestBounty extends Model
{
    use Auditable;

    /** @use HasFactory<\Database\Factories\TorrentRequestBountyFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'request_bounty';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array{seedbonus: 'decimal:2'}
     */
    protected function casts(): array
    {
        return [
            'seedbonus' => 'decimal:2',
            'anon'      => 'bool',
        ];
    }

    /**
     * Get the user that added the bounty.
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
     * Get the request that the bounty was added to.
     *
     * @return BelongsTo<TorrentRequest, $this>
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(TorrentRequest::class, 'requests_id');
    }
}
