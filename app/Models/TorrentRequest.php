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
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * App\Models\TorrentRequest.
 *
 * @property int                             $id
 * @property string                          $name
 * @property int                             $category_id
 * @property int|null                        $imdb
 * @property int|null                        $tvdb
 * @property int|null                        $tmdb_movie_id
 * @property int|null                        $tmdb_tv_id
 * @property int|null                        $mal
 * @property int                             $igdb
 * @property string                          $description
 * @property int                             $user_id
 * @property string                          $bounty
 * @property bool                            $anon
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null                        $filled_by
 * @property int|null                        $torrent_id
 * @property \Illuminate\Support\Carbon|null $filled_when
 * @property int                             $filled_anon
 * @property int|null                        $approved_by
 * @property \Illuminate\Support\Carbon|null $approved_when
 * @property int|null                        $type_id
 * @property int|null                        $resolution_id
 * @property int|null                        $season_number
 * @property int|null                        $episode_number
 */
class TorrentRequest extends Model
{
    use Auditable;

    /** @use HasFactory<\Database\Factories\TorrentRequestFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'requests';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array{
     *     filled_when: 'datetime',
     *     approved_when: 'datetime',
     *     tmdb_movie_id: 'int',
     *     tmdb_tv_id: 'int',
     *     igdb: 'int',
     *     bounty: 'decimal:2',
     *     anon: 'bool'
     * }
     */
    protected function casts(): array
    {
        return [
            'filled_when'   => 'datetime',
            'approved_when' => 'datetime',
            'tmdb_movie_id' => 'int',
            'tmdb_tv_id'    => 'int',
            'igdb'          => 'int',
            'bounty'        => 'decimal:2',
            'anon'          => 'bool',
        ];
    }

    /**
     * Get the user that owns the request.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault([
            'username' => 'System',
            'id'       => User::SYSTEM_USER_ID,
        ]);
    }

    /**
     * Get the approver of the request.
     *
     * @return BelongsTo<User, $this>
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by')->withDefault([
            'username' => 'System',
            'id'       => User::SYSTEM_USER_ID,
        ]);
    }

    /**
     * Get the filler of the request.
     *
     * @return BelongsTo<User, $this>
     */
    public function filler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'filled_by')->withDefault([
            'username' => 'System',
            'id'       => User::SYSTEM_USER_ID,
        ]);
    }

    /**
     * Get the category associated with the request.
     *
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the type associated with the request.
     *
     * @return BelongsTo<Type, $this>
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class);
    }

    /**
     * Get the resolution associated with the request.
     *
     * @return BelongsTo<Resolution, $this>
     */
    public function resolution(): BelongsTo
    {
        return $this->belongsTo(Resolution::class);
    }

    /**
     * Get the torrent that filled the request.
     *
     * @return BelongsTo<Torrent, $this>
     */
    public function torrent(): BelongsTo
    {
        return $this->belongsTo(Torrent::class);
    }

    /**
     * Get the movie associated with the request.
     *
     * @return BelongsTo<TmdbMovie, $this>
     */
    public function movie(): BelongsTo
    {
        return $this->belongsTo(TmdbMovie::class, 'tmdb_movie_id');
    }

    /**
     * Get the tv associated with the request.
     *
     * @return BelongsTo<TmdbTv, $this>
     */
    public function tv(): BelongsTo
    {
        return $this->belongsTo(TmdbTv::class, 'tmdb_tv_id');
    }

    /**
     * Get the game associated with the request.
     *
     * @return BelongsTo<IgdbGame, $this>
     */
    public function game(): BelongsTo
    {
        return $this->belongsTo(IgdbGame::class, 'igdb');
    }

    /**
     * Get the comments for the request.
     *
     * @return MorphMany<Comment, $this>
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Get the bounties for the request.
     *
     * @return HasMany<TorrentRequestBounty, $this>
     */
    public function bounties(): HasMany
    {
        return $this->hasMany(TorrentRequestBounty::class, 'requests_id', 'id');
    }

    /**
     * Get the claim associated with the request.
     *
     * @return HasOne<TorrentRequestClaim, $this>
     */
    public function claim(): HasOne
    {
        return $this->hasOne(TorrentRequestClaim::class, 'request_id');
    }
}
