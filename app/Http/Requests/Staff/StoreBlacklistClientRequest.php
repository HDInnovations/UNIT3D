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

use App\Models\BlacklistClient;
use Override;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Present;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

class StoreBlacklistClientRequest extends Data
{
    public function __construct(
        #[Max(255), Unique(BlacklistClient::class)]
        public string $name,
        #[Unique(BlacklistClient::class), Present]
        public string $peerIdPrefix = '',
        public string $reason = '',
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
        $properties['peer_id_prefix'] = $properties['peer_id_prefix'] ?? '';

        return $properties;
    }
}
