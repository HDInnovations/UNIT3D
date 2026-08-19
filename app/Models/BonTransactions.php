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
use Illuminate\Database\Eloquent\Attributes\DateFormat;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;

/**
 * App\Models\BonTransactions.
 *
 * @property int      $id
 * @property int      $bon_exchange_id
 * @property string   $name
 * @property string   $cost
 * @property int|null $sender_id
 * @property int|null $receiver_id
 * @property int|null $torrent_id
 * @property int|null $post_id
 * @property string   $comment
 * @property string   $created_at
 */
#[WithoutTimestamps]
#[DateFormat('U')]
#[Guarded('id', 'created_at', 'updated_at')]
#[AllowDynamicProperties]
final class BonTransactions extends Model
{
    /** @use HasFactory<\Database\Factories\BonTransactionsFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array{cost: 'decimal:2'}
     */
    #[Override]
    protected function casts(): array
    {
        return [
            'cost' => 'decimal:2',
        ];
    }

    /**
     * Get the user that sent the bon.
     *
     * @return BelongsTo<User, $this>
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault([
            'username' => 'System',
            'id'       => User::SYSTEM_USER_ID,
        ]);
    }

    /**
     * Get the user that received the bon.
     *
     * @return BelongsTo<User, $this>
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault([
            'username' => 'System',
            'id'       => User::SYSTEM_USER_ID,
        ]);
    }

    /**
     * Get the exchange that was transacted.
     *
     * @return BelongsTo<BonExchange, $this>
     */
    public function exchange(): BelongsTo
    {
        return $this->belongsTo(BonExchange::class)->withDefault([
            'value' => 0,
            'cost'  => 0,
        ]);
    }
}
