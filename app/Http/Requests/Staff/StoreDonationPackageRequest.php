<?php

declare(strict_types=1);

namespace App\Http\Requests\Staff;

use App\Models\DonationPackage;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

class StoreDonationPackageRequest extends Data
{
    public function __construct(
        public int $position,
        #[Unique(DonationPackage::class)]
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
