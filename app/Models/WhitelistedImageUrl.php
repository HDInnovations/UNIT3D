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
 * @author     Roardom <roardom@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use AllowDynamicProperties;

/**
 * App\Models\WhitelistedImageUrl.
 *
 * @property int    $id
 * @property string $pattern
 */
#[AllowDynamicProperties]
final class WhitelistedImageUrl extends Model
{
    use Auditable;

    /** @use HasFactory<\Database\Factories\WhitelistedImageUrlFactory> */
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];
}
