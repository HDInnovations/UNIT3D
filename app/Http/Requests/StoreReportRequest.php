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

use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;

#[MergeValidationRules]
class StoreReportRequest extends Data
{
    public function __construct(
        public ?int $reportedTorrentId,
        public ?int $reportedRequestId,
        public ?string $reportedUserUsername,
        #[Max(65535)]
        public string $message,
    ) {
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<string>>
     */
    public static function rules(): array
    {
        return [
            'reported_torrent_id' => [
                'required_without_all:reported_request_id,reported_user_username',
                'prohibits:reported_request_id,reported_user_username',
                'exists:torrents,id',
            ],
            'reported_request_id' => [
                'required_without_all:reported_torrent_id,reported_user_username',
                'prohibits:reported_torrent_id,reported_user_username',
                'exists:requests,id',
            ],
            'reported_user_username' => [
                'required_without_all:reported_torrent_id,reported_request_id',
                'prohibits:reported_torrent_id,reported_request_id',
                'exists:users,username',
            ],
        ];
    }
}
