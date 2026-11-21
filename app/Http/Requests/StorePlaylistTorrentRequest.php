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

use App\Models\Playlist;
use App\Models\Torrent;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;
use Illuminate\Validation\Rule as LaravelRule;

/**
 * @see \Tests\Todo\Unit\Http\Requests\VoteOnPollTest
 */
#[MergeValidationRules]
class StorePlaylistTorrentRequest extends Data
{
    public function __construct(
        #[Exists(Playlist::class, 'id')]
        public int $playlistId,
        #[Exists(Torrent::class, 'id')]
        public int $torrentId,
    ) {
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<Rule>|string>
     */
    public static function rules(ValidationContext $context): array
    {
        return [
            'playlist_id' => [
                new Rule([
                    LaravelRule::unique('playlist_torrents')->where('torrent_id', $context->payload['torrent_id']),
                ]),
            ],
            'torrent_id' => [
                new Rule([
                    LaravelRule::unique('playlist_torrents')->where('playlist_id', $context->payload['playlist_id']),
                ])
            ]
        ];
    }
}
