<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MediaEdition extends Model
{
    protected $guarded = [];

    /** @return HasMany<MediaVariant, $this> */
    public function variants(): HasMany
    {
        return $this->hasMany(MediaVariant::class);
    }
}
