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
use Illuminate\Database\Eloquent\SoftDeletes;
use AllowDynamicProperties;
use Override;

/**
 * App\Models\DonationPackage.
 *
 * @property int                        $id
 * @property int                        $position
 * @property string                     $name
 * @property string                     $description
 * @property float                      $cost
 * @property int|null                   $upload_value
 * @property int|null                   $invite_value
 * @property int|null                   $bonus_value
 * @property int|null                   $donor_value
 * @property bool                       $is_active
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
#[AllowDynamicProperties]
final class DonationPackage extends Model
{
    /** @use HasFactory<\Database\Factories\DonationPackageFactory> */
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array{position: 'int', name: 'string', description: 'string', cost: 'decimal:2', upload_value: 'int', invite_value: 'int', bonus_value: 'int', donor_value: 'int', is_active: 'bool', created_at: 'datetime', updated_at: 'datetime'}
     */
    #[Override]
    protected function casts(): array
    {
        return [
            'position'     => 'int',
            'name'         => 'string',
            'description'  => 'string',
            'cost'         => 'decimal:2',
            'upload_value' => 'int',
            'invite_value' => 'int',
            'bonus_value'  => 'int',
            'donor_value'  => 'int',
            'is_active'    => 'bool',
            'created_at'   => 'datetime',
            'updated_at'   => 'datetime',
        ];
    }
}
