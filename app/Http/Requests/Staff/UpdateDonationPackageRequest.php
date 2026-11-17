<?php

declare(strict_types=1);

namespace App\Http\Requests\Staff;

use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

class UpdateDonationPackageRequest extends Data
{
    public function __construct(
        #[Min(0)]
        public int $position,
        public string $name,
        public string $description,
        public int $cost,
        #[Min(0)]
        public int $bonusValue,
        #[Min(0)]
        public int $uploadValue,
        #[Min(0)]
        public int $inviteValue,
        #[Min(0)]
        public int $donorValue,
        public bool $isActive,
    ) {
    }
}
