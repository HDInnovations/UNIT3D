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

use AllowDynamicProperties;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\WhitelistedImageUrl.
 *
 * @property int    $id
 * @property string $pattern
 */
#[Unguarded]
#[AllowDynamicProperties]
final class WhitelistedImageUrl extends Model
{
    use Auditable;
}
