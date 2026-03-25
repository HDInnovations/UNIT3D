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

use App\Models\TorrentDeletionReason;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DestroyTorrentDeletionReasonRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<\Illuminate\Validation\Rules\Exists|string>>
     */
    public function rules(TorrentDeletionReason $torrentDeletionReason): array
    {
        return [
            'deletion_reason_id' => [
                'required',
                Rule::exists('torrent_deletion_reasons', 'id')->whereNot('id', (string) $torrentDeletionReason->id),
            ],
        ];
    }
}
