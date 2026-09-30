<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MediaVariant extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['audio_tracks' => 'array', 'subtitle_languages' => 'array'];
    }

    /** @return BelongsTo<MediaEdition, $this> */
    public function edition(): BelongsTo
    {
        return $this->belongsTo(MediaEdition::class, 'media_edition_id');
    }

    /** @return HasMany<Torrent, $this> */
    public function torrents(): HasMany
    {
        return $this->hasMany(Torrent::class);
    }
}
