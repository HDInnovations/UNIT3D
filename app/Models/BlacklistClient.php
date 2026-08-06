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
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BlacklistClient.
 *
 * @property int                             $id
 * @property string                          $name
 * @property string|null                     $reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string                          $peer_id_prefix
 */
#[Guarded('id', 'created_at', 'updated_at')]
#[AllowDynamicProperties]
final class BlacklistClient extends Model
{
    /** @use HasFactory<\Database\Factories\BlacklistClientFactory> */
    use HasFactory;
}
