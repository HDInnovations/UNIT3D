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
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Services\Metadata;

/**
 * Escapes free-text user input before it is embedded in a provider's
 * Lucene/Solr-flavoured search query syntax (MusicBrainz, Open Library) so a
 * plain search box can never be abused to inject field selectors, boolean
 * operators, or wildcards into the outbound provider request.
 */
final class ProviderQueryEscaper
{
    /**
     * @var list<string>
     */
    private const array RESERVED = [
        '\\', '+', '-', '!', '(', ')', '{', '}', '[', ']', '^', '"', '~', '*', '?', ':', '&', '|', '/',
    ];

    public static function lucene(string $query): string
    {
        $escaped = '';

        foreach (mb_str_split($query) as $char) {
            $escaped .= \in_array($char, self::RESERVED, true) ? '\\'.$char : $char;
        }

        return $escaped;
    }
}
