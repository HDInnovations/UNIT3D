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

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\Occupation;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TmdbTv.
 *
 * @property int                             $id
 * @property string|null                     $tmdb_id
 * @property string|null                     $imdb_id
 * @property string|null                     $tvdb_id
 * @property string|null                     $type
 * @property string                          $name
 * @property string                          $name_sort
 * @property string|null                     $overview
 * @property int|null                        $number_of_episodes
 * @property int|null                        $count_existing_episodes
 * @property int|null                        $count_total_episodes
 * @property int|null                        $number_of_seasons
 * @property string|null                     $episode_run_time
 * @property \Illuminate\Support\Carbon|null $first_air_date
 * @property string|null                     $status
 * @property string|null                     $homepage
 * @property int|null                        $in_production
 * @property \Illuminate\Support\Carbon|null $last_air_date
 * @property string|null                     $next_episode_to_air
 * @property string|null                     $origin_country
 * @property string|null                     $original_language
 * @property string|null                     $original_name
 * @property string|null                     $popularity
 * @property string|null                     $backdrop
 * @property string|null                     $poster
 * @property string|null                     $vote_average
 * @property int|null                        $vote_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null                     $trailer
 */
class TmdbTv extends Model
{
    /** @use HasFactory<\Database\Factories\TmdbTvFactory> */
    use HasFactory;

    protected $guarded = [];

    public $table = 'tmdb_tv';

    protected $hidden = ['created_at', 'updated_at'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array{first_air_date: 'datetime', last_air_date: 'datetime'}
     */
    protected function casts(): array
    {
        return [
            'first_air_date' => 'datetime',
            'last_air_date'  => 'datetime',
        ];
    }

    /**
     * Has Many Torrents.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Torrent, $this>
     */
    public function torrents(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Torrent::class)->whereRelation('category', 'tv_meta', '=', true);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<TmdbPerson, $this>
     */
    public function people(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(TmdbPerson::class, 'tmdb_credits');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<TmdbCredit, $this>
     */
    public function credits(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TmdbCredit::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<TmdbPerson, $this>
     */
    public function creators(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(TmdbPerson::class, 'tmdb_credits')
            ->wherePivot('occupation_id', '=', Occupation::CREATOR->value);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<TmdbGenre, $this>
     */
    public function genres(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(TmdbGenre::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<TmdbNetwork, $this>
     */
    public function networks(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(TmdbNetwork::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<TmdbCompany, $this>
     */
    public function companies(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(TmdbCompany::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<TmdbRecommendation, $this>
     */
    public function recommendations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TmdbRecommendation::class, 'tmdb_tv_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<TmdbTv, $this>
     */
    public function recommendedTv(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(__CLASS__, TmdbRecommendation::class, 'tmdb_tv_id', 'recommended_tmdb_tv_id', 'id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Wish, $this>
     */
    public function wishes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Wish::class);
    }
}
