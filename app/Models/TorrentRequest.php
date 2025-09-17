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
     * The Database Table Used By The Model.
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
     * Belongs To A User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, $this>
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault([
            'username' => 'System',
            'id'       => User::SYSTEM_USER_ID,
        ]);
    }

    /**
     * Belongs To A User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, $this>
     */
    public function approver(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by')->withDefault([
            'username' => 'System',
            'id'       => User::SYSTEM_USER_ID,
        ]);
    }

    /**
     * Belongs To A User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, $this>
     */
    public function filler(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'filled_by')->withDefault([
            'username' => 'System',
            'id'       => User::SYSTEM_USER_ID,
        ]);
    }

    /**
     * Belongs To A Category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Category, $this>
     */
    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Belongs To A Type.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Type, $this>
     */
    public function type(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Type::class);
    }

    /**
     * Belongs To A Resolution.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Resolution, $this>
     */
    public function resolution(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Resolution::class);
    }

    /**
     * Belongs To A Torrent.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Torrent, $this>
     */
    public function torrent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Torrent::class);
    }

    /**
     * Belongs To A Movie.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<TmdbMovie, $this>
     */
    public function movie(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TmdbMovie::class, 'tmdb_movie_id');
    }

    /**
     * Belongs To A Tv.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<TmdbTv, $this>
     */
    public function tv(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TmdbTv::class, 'tmdb_tv_id');
    }

    /**
     * Belongs To A Game.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<IgdbGame, $this>
     */
    public function game(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(IgdbGame::class, 'igdb_game_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Comment, $this>
     */
    public function comments(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Has Many BON Bounties.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<TorrentRequestBounty, $this>
     */
    public function bounties(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TorrentRequestBounty::class, 'requests_id', 'id');
    }

    /**
     * Has One Torrent Request Claim.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<TorrentRequestClaim, $this>
     */
    public function claim(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(TorrentRequestClaim::class, 'request_id');
    }
}
