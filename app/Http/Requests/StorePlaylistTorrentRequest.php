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

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * @see \Tests\Todo\Unit\Http\Requests\VoteOnPollTest
 */
class StorePlaylistTorrentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<\Illuminate\Validation\Rules\Exists|\Illuminate\Validation\Rules\Unique|string>>
     */
    public function rules(Request $request): array
    {
        return [
            'playlist_id' => [
                'required',
                'numeric',
                'integer',
                Rule::exists('playlists', 'id'),
                Rule::unique('playlist_torrents')->where('torrent_id', $request->integer('torrent_id')),
            ],
            'torrent_id' => [
                'required',
                'numeric',
                'integer',
                Rule::exists('torrents', 'id'),
                Rule::unique('playlist_torrents')->where('playlist_id', $request->integer('playlist_id')),
            ]
        ];
    }
}
