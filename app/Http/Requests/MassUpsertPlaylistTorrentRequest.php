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
use Illuminate\Validation\Rule;

/**
 * @see \Tests\Todo\Unit\Http\Requests\VoteOnPollTest
 */
class MassUpsertPlaylistTorrentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, \Illuminate\Validation\Rules\Exists|string>>
     */
    public function rules(): array
    {
        return [
            'playlist_id' => [
                'required',
                'numeric',
                'integer',
                Rule::exists('playlists', 'id'),
            ],
            'torrent_urls' => [
                'required',
                'max:65535',
            ],
        ];
    }
}
