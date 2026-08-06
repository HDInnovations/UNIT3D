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
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\WikiCategory.
 *
 * @property int    $id
 * @property string $name
 * @property string $icon
 * @property int    $position
 */
#[WithoutTimestamps]
#[Unguarded]
#[AllowDynamicProperties]
final class WikiCategory extends Model
{
    use Auditable;

    /**
     * Get the wikis for the wiki category.
     *
     * @return HasMany<Wiki, $this>
     */
    public function wikis(): HasMany
    {
        return $this->hasMany(Wiki::class, 'category_id');
    }
}
