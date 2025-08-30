<?php
namespace App\Models\MediaHub;

use Illuminate\Database\Eloquent\Model;

class PornStar extends Model
{
    protected $table = 'pornstars';
    protected $guarded = [];
    protected $casts = [
        'aliases' => 'array',
        'images' => 'array',
        'urls' => 'array',
        'birthdate' => 'array',
    ];

    public function scenes()
    {
        return $this->belongsToMany(StashDBScene::class, 'pornstar_scene', 'pornstar_id', 'scene_id');
    }

    public function torrents()
    {
        return $this->belongsToMany(\App\Models\Torrent::class, 'pornstar_torrent', 'pornstar_id', 'torrent_id');
    }
}
