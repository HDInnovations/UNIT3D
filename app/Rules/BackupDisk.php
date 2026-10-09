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

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Override;

class BackupDisk implements ValidationRule
{
    #[Override]
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!\in_array($value, config('backup.backup.destination.disks'))) {
            $fail('Current disk is not configured as a backup disk');
        }
    }
}
