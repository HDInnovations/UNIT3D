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
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\Models\TmdbPerson.
 *
 * @property int         $id
 * @property string      $name
 * @property string|null $imdb_id
 * @property string|null $known_for_department
 * @property string|null $place_of_birth
 * @property string|null $popularity
 * @property string|null $profile
 * @property string|null $still
 * @property string|null $adult
 * @property string|null $also_known_as
 * @property string|null $biography
 * @property string|null $birthday
 * @property string|null $deathday
 * @property string|null $gender
 * @property string|null $homepage
 */
class TmdbPerson extends Model
{
    /** @use HasFactory<\Database\Factories\TmdbPersonFactory> */
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    /**
     * Get the credits for the person.
     *
     * @return HasMany<TmdbCredit, $this>
     */
    public function credits(): HasMany
    {
        return $this->hasMany(TmdbCredit::class);
    }

    /**
     * Get the tv shows credited with the person.
     *
     * @return BelongsToMany<TmdbTv, $this, Pivot, 'credit'>
     */
    public function tv(): BelongsToMany
    {
        return $this->belongsToMany(TmdbTv::class, 'tmdb_credits')
            ->withPivot('character', 'occupation_id')
            ->as('credit');
    }

    /**
     * Get the tv shows created by the person.
     *
     * @return BelongsToMany<TmdbTv, $this, Pivot, 'credit'>
     */
    public function createdTv(): BelongsToMany
    {
        return $this->belongsToMany(TmdbTv::class, 'tmdb_credits')
            ->wherePivot('occupation_id', '=', Occupation::CREATOR)
            ->withPivot('character', 'occupation_id')
            ->as('credit');
    }

    /**
     * Get the tv shows directed by the person.
     *
     * @return BelongsToMany<TmdbTv, $this, Pivot, 'credit'>
     */
    public function directedTv(): BelongsToMany
    {
        return $this->belongsToMany(TmdbTv::class, 'tmdb_credits')
            ->wherePivot('occupation_id', '=', Occupation::DIRECTOR)
            ->withPivot('character', 'occupation_id')
            ->as('credit');
    }

    /**
     * Get the tv shows written by the person.
     *
     * @return BelongsToMany<TmdbTv, $this, Pivot, 'credit'>
     */
    public function writtenTv(): BelongsToMany
    {
        return $this->belongsToMany(TmdbTv::class, 'tmdb_credits')
            ->wherePivot('occupation_id', '=', Occupation::WRITER)
            ->withPivot('character', 'occupation_id')
            ->as('credit');
    }

    /**
     * Get the tv shows produced by the person.
     *
     * @return BelongsToMany<TmdbTv, $this, Pivot, 'credit'>
     */
    public function producedTv(): BelongsToMany
    {
        return $this->belongsToMany(TmdbTv::class, 'tmdb_credits')
            ->wherePivot('occupation_id', '=', Occupation::PRODUCER)
            ->withPivot('character', 'occupation_id')
            ->as('credit');
    }

    /**
     * Get the tv shows composed by the person.
     *
     * @return BelongsToMany<TmdbTv, $this, Pivot, 'credit'>
     */
    public function composedTv(): BelongsToMany
    {
        return $this->belongsToMany(TmdbTv::class, 'tmdb_credits')
            ->wherePivot('occupation_id', '=', Occupation::COMPOSER)
            ->withPivot('character', 'occupation_id')
            ->as('credit');
    }

    /**
     * Get the tv shows cinematographed by the person.
     *
     * @return BelongsToMany<TmdbTv, $this, Pivot, 'credit'>
     */
    public function cinematographedTv(): BelongsToMany
    {
        return $this->belongsToMany(TmdbTv::class, 'tmdb_credits')
            ->wherePivot('occupation_id', '=', Occupation::CINEMATOGRAPHER)
            ->withPivot('character', 'occupation_id')
            ->as('credit');
    }

    /**
     * Get the tv shows edited by the person.
     *
     * @return BelongsToMany<TmdbTv, $this, Pivot, 'credit'>
     */
    public function editedTv(): BelongsToMany
    {
        return $this->belongsToMany(TmdbTv::class, 'tmdb_credits')
            ->wherePivot('occupation_id', '=', Occupation::EDITOR)
            ->withPivot('character', 'occupation_id')
            ->as('credit');
    }

    /**
     * Get the tv shows production designed by the person.
     *
     * @return BelongsToMany<TmdbTv, $this, Pivot, 'credit'>
     */
    public function productionDesignedTv(): BelongsToMany
    {
        return $this->belongsToMany(TmdbTv::class, 'tmdb_credits')
            ->wherePivot('occupation_id', '=', Occupation::PRODUCTION_DESIGNER)
            ->withPivot('character', 'occupation_id')
            ->as('credit');
    }

    /**
     * Get the tv shows art directed by the person.
     *
     * @return BelongsToMany<TmdbTv, $this, Pivot, 'credit'>
     */
    public function artDirectedTv(): BelongsToMany
    {
        return $this->belongsToMany(TmdbTv::class, 'tmdb_credits')
            ->wherePivot('occupation_id', '=', Occupation::ART_DIRECTOR)
            ->withPivot('character', 'occupation_id')
            ->as('credit');
    }

    /**
     * Get the tv shows acted by the person.
     *
     * @return BelongsToMany<TmdbTv, $this, Pivot, 'credit'>
     */
    public function actedTv(): BelongsToMany
    {
        return $this->belongsToMany(TmdbTv::class, 'tmdb_credits')
            ->wherePivot('occupation_id', '=', Occupation::ACTOR)
            ->withPivot('character', 'occupation_id')
            ->as('credit');
    }

    /**
     * Get the movies credited with the person.
     *
     * @return BelongsToMany<TmdbMovie, $this, Pivot, 'credit'>
     */
    public function movie(): BelongsToMany
    {
        return $this->belongsToMany(TmdbMovie::class, 'tmdb_credits')
            ->withPivot('character', 'occupation_id')
            ->as('credit');
    }

    /**
     * Get the movies directed by the person.
     *
     * @return BelongsToMany<TmdbMovie, $this, Pivot, 'credit'>
     */
    public function directedMovies(): BelongsToMany
    {
        return $this->belongsToMany(TmdbMovie::class, 'tmdb_credits')
            ->wherePivot('occupation_id', '=', Occupation::DIRECTOR)
            ->withPivot('character', 'occupation_id')
            ->as('credit');
    }

    /**
     * Get the movies written by the person.
     *
     * @return BelongsToMany<TmdbMovie, $this, Pivot, 'credit'>
     */
    public function writtenMovies(): BelongsToMany
    {
        return $this->belongsToMany(TmdbMovie::class, 'tmdb_credits')
            ->wherePivot('occupation_id', '=', Occupation::WRITER)
            ->withPivot('character', 'occupation_id')
            ->as('credit');
    }

    /**
     * Get the movies produced by the person.
     *
     * @return BelongsToMany<TmdbMovie, $this, Pivot, 'credit'>
     */
    public function producedMovies(): BelongsToMany
    {
        return $this->belongsToMany(TmdbMovie::class, 'tmdb_credits')
            ->wherePivot('occupation_id', '=', Occupation::PRODUCER)
            ->withPivot('character', 'occupation_id')
            ->as('credit');
    }

    /**
     * Get the movies composed by the person.
     *
     * @return BelongsToMany<TmdbMovie, $this, Pivot, 'credit'>
     */
    public function composedMovies(): BelongsToMany
    {
        return $this->belongsToMany(TmdbMovie::class, 'tmdb_credits')
            ->wherePivot('occupation_id', '=', Occupation::COMPOSER)
            ->withPivot('character', 'occupation_id')
            ->as('credit');
    }

    /**
     * Get the movies cinematographed by the person.
     *
     * @return BelongsToMany<TmdbMovie, $this, Pivot, 'credit'>
     */
    public function cinematographedMovies(): BelongsToMany
    {
        return $this->belongsToMany(TmdbMovie::class, 'tmdb_credits')
            ->wherePivot('occupation_id', '=', Occupation::CINEMATOGRAPHER)
            ->withPivot('character', 'occupation_id')
            ->as('credit');
    }

    /**
     * Get the movies edited by the person.
     *
     * @return BelongsToMany<TmdbMovie, $this, Pivot, 'credit'>
     */
    public function editedMovies(): BelongsToMany
    {
        return $this->belongsToMany(TmdbMovie::class, 'tmdb_credits')
            ->wherePivot('occupation_id', '=', Occupation::EDITOR)
            ->withPivot('character', 'occupation_id')
            ->as('credit');
    }

    /**
     * Get the movies production designed by the person.
     *
     * @return BelongsToMany<TmdbMovie, $this, Pivot, 'credit'>
     */
    public function productionDesignedMovies(): BelongsToMany
    {
        return $this->belongsToMany(TmdbMovie::class, 'tmdb_credits')
            ->wherePivot('occupation_id', '=', Occupation::PRODUCTION_DESIGNER)
            ->withPivot('character', 'occupation_id')
            ->as('credit');
    }

    /**
     * Get the movies art directed by the person.
     *
     * @return BelongsToMany<TmdbMovie, $this, Pivot, 'credit'>
     */
    public function artDirectedMovies(): BelongsToMany
    {
        return $this->belongsToMany(TmdbMovie::class, 'tmdb_credits')
            ->wherePivot('occupation_id', '=', Occupation::ART_DIRECTOR)
            ->withPivot('character', 'occupation_id')
            ->as('credit');
    }

    /**
     * Get the movies acted by the person.
     *
     * @return BelongsToMany<TmdbMovie, $this, Pivot, 'credit'>
     */
    public function actedMovies(): BelongsToMany
    {
        return $this->belongsToMany(TmdbMovie::class, 'tmdb_credits')
            ->wherePivot('occupation_id', '=', Occupation::ACTOR)
            ->withPivot('character', 'occupation_id')
            ->as('credit');
    }
}
