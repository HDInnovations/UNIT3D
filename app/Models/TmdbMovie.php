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
 * App\Models\TmdbMovie.
 *
 * @property int                             $id
 * @property string|null                     $tmdb_id
 * @property string|null                     $imdb_id
 * @property string                          $title
 * @property string                          $title_sort
 * @property string|null                     $original_language
 * @property int|null                        $adult
 * @property string|null                     $certification
 * @property array|null                      $content_ratings
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
     * @return array{release_date: 'datetime', content_ratings: 'array'}
     */
    protected function casts(): array
    {
        return [
            'release_date' => 'datetime',
            'content_ratings' => 'array',
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<TmdbGenre, $this>
     */
    public function genres(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(TmdbGenre::class);
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
    public function directors(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(TmdbPerson::class, 'tmdb_credits')
            ->wherePivot('occupation_id', '=', Occupation::DIRECTOR->value);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<TmdbCompany, $this>
     */
    public function companies(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(TmdbCompany::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<TmdbCollection, $this>
     */
    public function collections(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(TmdbCollection::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<TmdbMovie, $this>
     */
    public function recommendedMovies(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(__CLASS__, 'tmdb_recommended_movies', 'tmdb_movie_id', 'recommended_tmdb_movie_id', 'id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Torrent, $this>
     */
    public function torrents(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Torrent::class)->whereRelation('category', 'movie_meta', '=', true);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<TorrentRequest, $this>
     */
    public function requests(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TorrentRequest::class)->whereRelation('category', 'movie_meta', '=', true);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Wish, $this>
     */
    public function wishes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Wish::class);
    }
}
