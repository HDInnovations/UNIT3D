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
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Override;

/**
 * App\Models\BonExchange.
 *
 * @property int         $id
 * @property string|null $description
 * @property int         $value
 * @property int         $cost
 * @property bool        $upload
 * @property bool        $download
 * @property bool        $personal_freeleech
 * @property bool        $invite
 */
#[WithoutTimestamps]
#[Guarded('id')]
#[AllowDynamicProperties]
final class BonExchange extends Model
{
    /** @use HasFactory<\Database\Factories\BonExchangeFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array{upload: 'bool', download: 'bool', personal_freeleech: 'bool', invite: 'bool'}
     */
    #[Override]
    protected function casts(): array
    {
        return [
            'upload'             => 'bool',
            'download'           => 'bool',
            'personal_freeleech' => 'bool',
            'invite'             => 'bool',
        ];
    }

}
