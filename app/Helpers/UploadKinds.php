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

namespace App\Helpers;

use App\Models\Category;

/**
 * Shared mapping between torrent categories/types and the upload "kind" they
 * belong to (movie, tv, game, music, xxx, book, no). Reused by both the
 * upload form (TorrentController::create) and validation (StoreTorrentRequest)
 * so the two never drift apart.
 */
final class UploadKinds
{
    /**
     * @var list<string>
     */
    public const array KINDS = ['movie', 'tv', 'game', 'music', 'xxx', 'book', 'no'];

    /**
     * Explicit mapping of built-in/known type names to the upload kinds they
     * apply to. Any type name not present here (including admin-created
     * custom types) is treated as applicable to every kind, matching the
     * behaviour of the pre-existing "Other"/generic types.
     *
     * @var array<string, list<string>>
     */
    private const array TYPE_KIND_MAP = [
        'Full Disc' => ['movie', 'tv', 'xxx'],
        'Remux'     => ['movie', 'tv', 'xxx'],
        'Encode'    => ['movie', 'tv', 'xxx'],
        'WEB-DL'    => ['movie', 'tv', 'xxx'],
        'WEBRip'    => ['movie', 'tv', 'xxx'],
        'HDTV'      => ['movie', 'tv', 'xxx'],
        'PC'        => ['game'],
        'Console'   => ['game'],
        'Lossless'  => ['music'],
        'Lossy'     => ['music'],
        // 'Other' is intentionally absent so it (and any unknown custom type)
        // falls back to applying to every upload kind.
    ];

    /**
     * Determine the upload kind of a category from its metadata flags/name.
     */
    public static function categoryKind(Category $category): string
    {
        return match (true) {
            $category->movie_meta     => 'movie',
            $category->tv_meta        => 'tv',
            $category->game_meta      => 'game',
            $category->music_meta     => 'music',
            $category->book_meta      => 'book',
            $category->name === 'XXX' => 'xxx',
            default                   => 'no',
        };
    }

    /**
     * The list of upload kinds a given type name is applicable to.
     *
     * @return list<string>
     */
    public static function typeKinds(string $typeName): array
    {
        return self::TYPE_KIND_MAP[$typeName] ?? self::KINDS;
    }

    /**
     * Whether a type (by name) is applicable to the given upload kind.
     */
    public static function typeAppliesToKind(string $typeName, string $kind): bool
    {
        return \in_array($kind, self::typeKinds($typeName), true);
    }
}
