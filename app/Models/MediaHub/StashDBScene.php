<?php

namespace App\Models\MediaHub;

use Illuminate\Database\Eloquent\Model;

class StashDBScene extends Model
{
    public function pornstars()
    {
        return $this->belongsToMany(PornStar::class, 'pornstar_scene', 'scene_id', 'pornstar_id');
    }
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
