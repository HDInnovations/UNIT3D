<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TorrentMetadata extends Model
{
    /**
     * @var list<string>
     */
    protected $guarded = [];

    /**
     * @return array{raw: 'array', facets: 'array'}
     */
    protected function casts(): array
    {
        return [
            'raw'    => 'array',
            'facets' => 'array',
        ];
    }

    /**
     * @return BelongsTo<Torrent, $this>
     */
    public function torrent(): BelongsTo
    {
        return $this->belongsTo(Torrent::class);
    }
}
