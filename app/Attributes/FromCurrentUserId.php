<?php

namespace App\Attributes;

use Attribute;
use Spatie\LaravelData\Attributes\FromAuthenticatedUserProperty;

#[Attribute(Attribute::TARGET_PROPERTY)]
class FromCurrentUserId extends FromAuthenticatedUserProperty
{
    public function __construct() {
        parent::__construct(null, 'id', true);
    }
}
