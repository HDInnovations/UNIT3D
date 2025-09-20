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
 * App\Models\TmdbMovie.
 *
 * @property int                             $id
 * @property string|null                     $tmdb_id
 * @property string|null                     $imdb_id
 * @property string                          $title
 * @property string                          $title_sort
 * @property string|null                     $original_language
 * @property int|null                        $adult
 * @property string|null                     $backdrop
 * @property string|null                     $budget
 * @property string|null                     $homepage
 * @property string|null                     $original_title
 * @property string|null                     $overview
 * @property string|null                     $popularity
 * @property string|null                     $poster
 * @property \Illuminate\Support\Carbon|null $release_date
 * @property string|null                     $revenue
 * @property string|null                     $runtime
 * @property string|null                     $status
 * @property string|null                     $tagline
 * @property string|null                     $vote_average
 * @property int|null                        $vote_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null                     $trailer
 */
class TmdbMovie extends Model
{
    /** @use HasFactory<\Database\Factories\TmdbMovieFactory> */
    use HasFactory;

    protected $guarded = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array{release_date: 'datetime'}
     */
    protected function casts(): array
    {
        return [
            'release_date' => 'datetime',
        ];
    }

    /**
     * Get the genres that belong to the movie.
     *
     * @return BelongsToMany<TmdbGenre, $this>
     */
    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(TmdbGenre::class);
    }

    /**
     * Get the people that belong to the movie.
     *
     * @return BelongsToMany<TmdbPerson, $this>
     */
    public function people(): BelongsToMany
    {
        return $this->belongsToMany(TmdbPerson::class, 'tmdb_credits');
    }

    /**
     * Get the credits for the movie.
     *
     * @return HasMany<TmdbCredit, $this>
     */
    public function credits(): HasMany
    {
        return $this->hasMany(TmdbCredit::class);
    }

    /**
     * Get the directors that belong to the movie.
     *
     * @return BelongsToMany<TmdbPerson, $this>
     */
    public function directors(): BelongsToMany
    {
        return $this->belongsToMany(TmdbPerson::class, 'tmdb_credits')
            ->wherePivot('occupation_id', '=', Occupation::DIRECTOR);
    }

    /**
     * Get the actors that belong to the movie.
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
     * Get the companies that belong to the movie.
     *
     * @return BelongsToMany<TmdbCompany, $this>
     */
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(TmdbCompany::class);
    }

    /**
     * Get the collections that belong to the movie.
     *
     * @return BelongsToMany<TmdbCollection, $this>
     */
    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(TmdbCollection::class);
    }

    /**
     * Get the recommended movies that belong to the movie.
     *
     * @return BelongsToMany<TmdbMovie, $this>
     */
    public function recommendedMovies(): BelongsToMany
    {
        return $this->belongsToMany(__CLASS__, 'tmdb_recommended_movies', 'tmdb_movie_id', 'recommended_tmdb_movie_id', 'id', 'id');
    }

    /**
     * Get the torrents for the movie.
     *
     * @return HasMany<Torrent, $this>
     */
    public function torrents(): HasMany
    {
        return $this->hasMany(Torrent::class)->whereRelation('category', 'movie_meta', '=', true);
    }

    /**
     * Get the requests for the movie.
     *
     * @return HasMany<TorrentRequest, $this>
     */
    public function requests(): HasMany
    {
        return $this->hasMany(TorrentRequest::class)->whereRelation('category', 'movie_meta', '=', true);
    }

    /**
     * Get the wishes for the movie.
     *
     * @return HasMany<Wish, $this>
     */
    public function wishes(): HasMany
    {
        return $this->hasMany(Wish::class);
    }
}
