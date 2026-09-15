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

use AllowDynamicProperties;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;

/**
 * App\Models\TorrentRequestBounty.
 *
 * @property int                             $id
 * @property int                             $user_id
 * @property string                          $seedbonus
 * @property int                             $request_id
 * @property bool                            $anon
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
#[Table('request_bounty')]
#[Guarded('id', 'created_at', 'updated_at')]
#[AllowDynamicProperties]
final class TorrentRequestBounty extends Model
{
    use Auditable;

    /** @use HasFactory<\Database\Factories\TorrentRequestBountyFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array{seedbonus: 'decimal:2'}
     */
    #[Override]
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
        return $this->belongsTo(TorrentRequest::class);
    }
}
