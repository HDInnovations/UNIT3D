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

use App\Enums\ModerationStatus;
use App\Traits\Encryptable;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Donation.
 *
 * @property int                             $id
 * @property int                             $user_id
 * @property int                             $gifted_user_id
 * @property ModerationStatus                $status
 * @property int                             $package_id
 * @property string                          $transaction
 * @property bool                            $is_gifted
 * @property \Illuminate\Support\Carbon      $starts_at
 * @property \Illuminate\Support\Carbon|null $ends_at
 * @property \Illuminate\Support\Carbon      $created_at
 * @property \Illuminate\Support\Carbon      $updated_at
 */
class Donation extends Model
{
    use Encryptable;

    final public const int PENDING = 0;
    final public const int APPROVED = 1;
    final public const int REJECTED = 2;

    /**
     * Get the attributes that should be cast.
     *
     * @return array{user_id: 'int', gifted_user_id: 'int', status: class-string<ModerationStatus>, package_id: 'int', transaction: 'string', is_gifted: 'bool', starts_at: 'datetime', ends_at: 'datetime', created_at: 'datetime', updated_at: 'datetime'}
     */
    protected function casts(): array
    {
        return [
            'user_id'        => 'int',
            'gifted_user_id' => 'int',
            'status'         => ModerationStatus::class,
            'package_id'     => 'int',
            'transaction'    => 'string',
            'is_gifted'      => 'bool',
            'starts_at'      => 'datetime',
            'ends_at'        => 'datetime',
            'created_at'     => 'datetime',
            'updated_at'     => 'datetime',
        ];
    }

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]
     */
    protected $guarded = ['id'];

    /**
     * The Attributes That Are Encrypted.
     *
     * @var string[]
     */
    protected array $encryptable = [
        'transaction',
    ];

    /**
     * Belongs To A User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, $this>
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Belongs To A Package.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<DonationPackage, $this>
     */
    public function package(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DonationPackage::class)->withTrashed();
    }
}
