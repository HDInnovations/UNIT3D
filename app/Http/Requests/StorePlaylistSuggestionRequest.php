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

namespace App\Http\Requests;

use App\Attributes\FromCurrentUserId;
use App\Models\PlaylistSuggestion;
use App\Models\Torrent;
use Override;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\Constraints\WhereConstraint;
use Spatie\LaravelData\Support\Validation\References\RouteParameterReference;

class StorePlaylistSuggestionRequest extends Data
{
    public function __construct(
        #[Exists(Torrent::class, 'id'), Unique(PlaylistSuggestion::class, where: new WhereConstraint('playlist_id', new RouteParameterReference('playlist', 'id')))]
        public int $torrentId,
        #[Max(65535)]
        public ?string $message,
        #[FromCurrentUserId]
        public int $userId,
    ) {
    }

    /**
     * @param array<mixed> $properties
     *
     * @return array<mixed>
     */
    #[Override]
    public static function prepareForPipeline(array $properties): array
    {
        $properties['torrent_id'] = basename($properties['torrent_url'] ?? '');

        return $properties;
    }

    /**
     * @return array<string, string>
     */
    public static function messages(): array
    {
        return [
            'torrent_id.exists' => 'The torrent ID/URL ":input" entered was not found on site.',
            'torrent_id.unique' => 'This torrent ID/URL ":input" entered is already suggested and awaiting moderation.',
        ];
    }
}
