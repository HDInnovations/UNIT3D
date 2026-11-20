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

namespace App\Http\Requests\User;

use App\Attributes\FromCurrentUserId;
use App\Models\Torrent;
use App\Models\User;
use Override;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\Between;
use Spatie\LaravelData\Attributes\Validation\Different;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\References\FieldReference;
use Spatie\LaravelData\Support\Validation\ValidationRule;

#[MergeValidationRules]
class StoreTorrentTipRequest extends Data
{
    public function __construct(
        #[Exists(Torrent::class, 'id')]
        public int $torrentId,
        public int $bon,
        #[FromCurrentUserId]
        public int $senderId,
        #[Exists(User::class, 'id'), Different(new FieldReference('sender_id'))]
        public int $recipientId,
    ) {
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<ValidationRule>>
     */
    public static function rules(): array
    {
        return [
            'bon' => [
                new Between(1, (float) auth()->user()->seedbonus),
            ]
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public static function messages(): array
    {
        return [
            'recipient_id.different' => trans('bon.failed-yourself'),
        ];
    }

    /**
     * @param array<array-key, mixed> $properties
     *
     * @return array<array-key, mixed>
     */
    #[Override]
    public static function prepareForPipeline(array $properties): array
    {
        $properties['recipient_id'] = Torrent::query()
            ->whereKey((int) $properties['torrent_id'])
            ->value('user_id');

        return $properties;
    }
}
