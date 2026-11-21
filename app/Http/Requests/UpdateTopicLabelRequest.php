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
 * @author     HDVinnie <hdinnovations@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Http\Requests;

use Spatie\LaravelData\Data;

class UpdateTopicLabelRequest extends Data
{
    public function __construct(
        public ?bool $approved = false,
        public ?bool $denied = false,
        public ?bool $solved = false,
        public ?bool $invalid = false,
        public ?bool $bug = false,
        public ?bool $suggestion = false,
        public ?bool $implemented = false,
    ) {
    }
}
