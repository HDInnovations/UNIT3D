<?php

namespace App\Models\MediaHub;

use Illuminate\Database\Eloquent\Model;

class StashDBScene extends Model
{
    protected $table = 'stashdb_scenes';
    protected $guarded = [];
    protected $casts = [
        'urls' => 'array',
        'tags' => 'array',
        'performers' => 'array',
        'fingerprints' => 'array',
        'images' => 'array',
    ];
}
