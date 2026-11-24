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

use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Attributes\Hidden;
use Spatie\LaravelData\Attributes\Validation\Exclude;
use Spatie\LaravelData\Attributes\Validation\File;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;

class StoreCategoryRequest extends Data
{
    public bool $movieMeta {
        get => $this->meta === 'movie';
    }

    public bool $tvMeta {
        get => $this->meta === 'tv';
    }

    public bool $gameMeta {
        get => $this->meta === 'game';
    }

    public bool $musicMeta {
        get => $this->meta === 'music';
    }

    public bool $noMeta {
        get => $this->meta === 'no';
    }

    public function __construct(
        public string $name,
        public int $position,
        public string $icon,
        #[In('movie', 'tv', 'game', 'music', 'no'), Exclude, Hidden]
        public string $meta,
        #[File, Max(10240)]
        public ?UploadedFile $image,
    ) {
    }
}
