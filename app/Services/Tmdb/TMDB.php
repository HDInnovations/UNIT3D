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
     * Merge a localized TMDB response with an English response only when a
     * user-facing localized field is absent. The primary locale always wins.
     *
     * @param  array<string, mixed> $localized
     * @param  array<string, mixed> $english
     * @return array<string, mixed>
     */
    public function withEnglishFallback(array $localized, array $english): array
    {
        foreach ($english as $key => $value) {
            if (!\array_key_exists($key, $localized) || $this->isBlank($localized[$key])) {
                $localized[$key] = $value;

                continue;
            }

            if (
                \is_array($localized[$key])
                && \is_array($value)
                && !array_is_list($localized[$key])
                && !array_is_list($value)
            ) {
                $localized[$key] = $this->withEnglishFallback($localized[$key], $value);
            }
        }

        return $localized;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function needsEnglishFallback(array $data): bool
    {
        foreach (['name', 'title', 'overview'] as $key) {
            if (\array_key_exists($key, $data) && $this->isBlank($data[$key])) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param mixed $value
     */
    private function isBlank(mixed $value): bool
    {
        return $value === null || $value === '' || $value === [];
    }

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
        if (isset($array['videos']['results'])) {
            return 'https://www.youtube.com/embed/'.$array['videos']['results'][0]['key'];
        }

        return null;
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
