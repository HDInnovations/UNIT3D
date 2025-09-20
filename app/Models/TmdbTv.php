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
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\TmdbTv.
 *
 * @property int                             $id
 * @property string|null                     $tmdb_id
 * @property bool|null                       $adult
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
     * Get torrents for the tv show.
     *
     * @return HasMany<Torrent, $this>
     */
    public function torrents(): HasMany
    {
        return $this->hasMany(Torrent::class)->whereRelation('category', 'tv_meta', '=', true);
    }

    /**
     * Get the people that belong to the tv show.
     *
     * @return BelongsToMany<TmdbPerson, $this>
     */
    public function people(): BelongsToMany
    {
        return $this->belongsToMany(TmdbPerson::class, 'tmdb_credits');
    }

    /**
     * Get the credits for the tv show.
     *
     * @return HasMany<TmdbCredit, $this>
     */
    public function credits(): HasMany
    {
        return $this->hasMany(TmdbCredit::class);
    }

    /**
     * Get the creators that belong to the tv show.
     *
     * @return BelongsToMany<TmdbPerson, $this>
     */
    public function creators(): BelongsToMany
    {
        return $this->belongsToMany(TmdbPerson::class, 'tmdb_credits')
            ->wherePivot('occupation_id', '=', Occupation::CREATOR);
    }

    /**
     * Get the actors that belong to the tv show.
     *
     * @return BelongsToMany<TmdbPerson, $this>
     */
    public function actors(): BelongsToMany
    {
        return $this->belongsToMany(TmdbPerson::class, 'tmdb_credits')
            ->wherePivot('occupation_id', '=', Occupation::ACTOR)
            ->orderByPivot('order');
    }

    /**
     * Get the genres that belong to the tv show.
     *
     * @return BelongsToMany<TmdbGenre, $this>
     */
    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(TmdbGenre::class);
    }

    /**
     * Get the networks that belong to the tv show.
     *
     * @return BelongsToMany<TmdbNetwork, $this>
     */
    public function networks(): BelongsToMany
    {
        return $this->belongsToMany(TmdbNetwork::class);
    }

    /**
     * Get the companies that belong to the tv show.
     *
     *
     * @return BelongsToMany<TmdbCompany, $this>
     */
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(TmdbCompany::class);
    }

    /**
     * Get the recommended tv shows that belong to the tv show.
     *
     * @return BelongsToMany<TmdbTv, $this>
     */
    public function recommendedTv(): BelongsToMany
    {
        return $this->belongsToMany(__CLASS__, 'tmdb_recommended_tv', 'tmdb_tv_id', 'recommended_tmdb_tv_id', 'id', 'id');
    }

    /**
     * Get the wishes for this tv show.
     *
     * @return HasMany<Wish, $this>
     */
    public function wishes(): HasMany
    {
        return $this->hasMany(Wish::class);
    }
}
