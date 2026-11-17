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

namespace App\Http\Requests\Staff;

use Spatie\LaravelData\Attributes\Hidden;
use Spatie\LaravelData\Attributes\Validation\Exclude;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Data;

class UpdateBonExchangeRequest extends Data
{
    public bool $upload {
        get => $this->type === 'upload';
    }

    public bool $download {
        get => $this->type === 'download';
    }

    public bool $personalFreeleech {
        get => $this->type === 'personal_freeleech';
    }

    public bool $invite {
        get => $this->type === 'invite';
    }

    public function __construct(
        public string $description,
        public int $value,
        public int $cost,
        #[In('upload', 'download', 'personal_freeleech', 'invite'), Exclude, Hidden]
        public string $type,
    ) {
    }
}
