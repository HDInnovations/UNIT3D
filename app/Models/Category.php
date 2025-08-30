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

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Category.
 *
 * @property int         $id
 * @property string      $name
 * @property string|null $image
 * @property int         $position
 * @property string      $icon
 * @property int         $no_meta
 * @property bool        $music_meta
 * @property bool        $game_meta
 * @property bool        $tv_meta
 * @property bool        $movie_meta
 */
class Category extends Model
{
    use Auditable;

    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

    /**
     * Indicates If The Model Should Be Timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the attributes that should be cast.
     *
     * @return array{music_meta: 'bool', game_meta: 'bool', tv_meta: 'bool', movie_meta: 'bool', porn_meta: 'bool'}
     */
    protected function casts(): array
    {
        return [
            'music_meta' => 'bool',
            'game_meta'  => 'bool',
            'tv_meta'    => 'bool',
            'movie_meta' => 'bool',
            'porn_meta'  => 'bool',
        ];
    }

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]
     */
    protected $guarded = ['id'];

    /**
     * Has Many Torrents.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Torrent, $this>
     */
    public function torrents(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Torrent::class);
    }

    /**
     * Has Many Requests.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<TorrentRequest, $this>
     */
    public function requests(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TorrentRequest::class);
    }
}
