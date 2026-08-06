<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Facade;

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

return [
    'meta_locale' => 'en_US',

    'aliases' => Facade::defaultAliases()->merge([
        'CacheUser' => App\Helpers\CacheUser::class,
        'Image'     => Intervention\Image\Facades\Image::class,
        'Irc'       => App\Bots\IRCAnnounceBot::class,
        'Redis'     => Illuminate\Support\Facades\Redis::class,
    ])->toArray(),
];
