<?php
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

use Illuminate\Database\Eloquent\Model;

class Tv extends Model
{
    protected $guarded = [];
    public $table = 'tv';
    protected $hidden = ['created_at', 'updated_at'];

    /**
     * Has Many Torrents.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function torrents()
    {
        return $this->hasMany(Torrent::class, 'tmdb', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function seasons()
    {
        return $this->hasMany(Season::class)
            ->orderBy('season_number');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function persons()
    {
        return $this->belongsToMany(Person::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function cast()
    {
        return $this->belongsToMany(Cast::class, 'cast_tv', 'cast_id', 'tv_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function crew()
    {
        return $this->belongsToMany(Crew::class, 'crew_tv', 'person_id', 'tv_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function creators()
    {
        return $this->belongsToMany(Person::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function networks()
    {
        return $this->belongsToMany(Network::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function companies()
    {
        return $this->belongsToMany(Company::class);
    }
}
