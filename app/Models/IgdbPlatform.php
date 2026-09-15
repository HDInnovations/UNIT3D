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
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\IgdbPlatform.
 *
 * @property int     $id
 * @property string  $name
 * @property ?string $platform_logo_image_id
 */
#[WithoutTimestamps]
#[Unguarded]
#[AllowDynamicProperties]
final class IgdbPlatform extends Model
{
    /**
     * Get the games that belong to the platform.
     *
     * @return BelongsToMany<IgdbGame, $this>
     */
    public function games(): BelongsToMany
    {
        return $this->belongsToMany(IgdbGame::class);
    }
}
