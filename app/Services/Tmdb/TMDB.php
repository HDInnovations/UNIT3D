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

namespace App\Services\Tmdb;

class TMDB
{
    /**
     * @param array<mixed> $array
     */
    public function image(string $type, array $array): ?string
    {
        if (isset($array[$type.'_path'])) {
            return 'https://image.tmdb.org/t/p/original'.$array[$type.'_path'];
        }

        return null;
    }

    /**
     * @param array<mixed> $array
     */
    public function trailer(array $array): ?string
    {
        foreach ($array['videos']['results'] ?? [] as $video) {
            if (!\is_array($video) || strcasecmp((string) ($video['type'] ?? ''), 'Trailer') !== 0) {
                continue;
            }

            $key = $video['key'] ?? null;
            $site = strtolower((string) ($video['site'] ?? ''));

            if (!\is_string($key) || $key === '' || !\in_array($site, ['youtube', 'vimeo'], true)) {
                continue;
            }

            return $site.':'.$key;
        }

        return null;
    }

    public function trailerEmbedUrl(?string $trailer): ?string
    {
        if ($trailer === null || $trailer === '') {
            return null;
        }

        [$site, $key] = str_contains($trailer, ':')
            ? explode(':', $trailer, 2)
            : ['youtube', $trailer];

        if ($key === '') {
            return null;
        }

        return match (strtolower($site)) {
            'youtube' => 'https://www.youtube-nocookie.com/embed/'.$key,
            'vimeo'   => 'https://player.vimeo.com/video/'.$key,
            default   => null,
        };
    }

    /**
     * @param array<mixed> $array
     */
    public function ifHasItems(string $type, array $array): mixed
    {
        return $array[$type][0] ?? null;
    }

    /**
     * @param array<mixed> $array
     */
    public function ifExists(string $type, array $array): mixed
    {
        if (isset($array[$type]) && !empty($array[$type])) {
            return $array[$type];
        }

        return null;
    }
}
