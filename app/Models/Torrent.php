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

use App\Enums\ModerationStatus;
use App\Helpers\StringHelper;
use App\Models\Scopes\ApprovedScope;
use App\Notifications\NewComment;
use App\Notifications\NewThank;
use App\Traits\Auditable;
use App\Traits\GroupedLastScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

/**
 * App\Models\Torrent.
 *
 * @property string                          $info_hash
 * @property int                             $id
 * @property string                          $name
 * @property string                          $description
 * @property string|null                     $mediainfo
 * @property string|null                     $bdinfo
 * @property string                          $file_name
 * @property int                             $num_file
 * @property string|null                     $folder
 * @property float                           $size
 * @property mixed|null                      $nfo
 * @property int                             $leechers
 * @property int                             $seeders
 * @property int                             $times_completed
 * @property int|null                        $category_id
 * @property int                             $user_id
 * @property int                             $imdb
 * @property int                             $tvdb
 * @property int|null                        $tmdb_movie_id
 * @property int|null                        $tmdb_tv_id
 * @property int                             $mal
 * @property int                             $igdb
 * @property int|null                        $season_number
 * @property int|null                        $episode_number
 * @property int                             $free
 * @property bool                            $doubleup
 * @property bool                            $refundable
 * @property int                             $highspeed
 * @property ModerationStatus                $status
 * @property \Illuminate\Support\Carbon|null $moderated_at
 * @property int|null                        $moderated_by
 * @property bool                            $anon
 * @property bool                            $sticky
 * @property int                             $internal
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $bumped_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $fl_until
 * @property \Illuminate\Support\Carbon|null $du_until
 * @property int                             $type_id
 * @property int|null                        $resolution_id
 * @property int|null                        $distributor_id
 * @property int|null                        $region_id
 * @property bool                            $personal_release
 * @property int|null                        $balance
 * @property int|null                        $balance_offset
 */
class Torrent extends Model
{
    use Auditable;
    use GroupedLastScope;

    /** @use HasFactory<\Database\Factories\TorrentFactory> */
    use HasFactory;
    use Searchable;
    use SoftDeletes;

    protected $guarded = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array{
     *     tmdb_movie_id: 'int',
     *     tmdb_tv_id: 'int',
     *     igdb: 'int',
     *     status: class-string<ModerationStatus>,
     *     bumped_at: 'datetime',
     *     fl_until: 'datetime',
     *     du_until: 'datetime',
     *     doubleup: 'bool',
     *     refundable: 'bool',
     *     moderated_at: 'datetime',
     *     anon: 'bool',
     *     sticky: 'bool',
     *     personal_release: 'bool'
     * }
     */
    protected function casts(): array
    {
        return [
            'tmdb_movie_id'    => 'int',
            'tmdb_tv_id'       => 'int',
            'igdb'             => 'int',
            'bumped_at'        => 'datetime',
            'fl_until'         => 'datetime',
            'du_until'         => 'datetime',
            'doubleup'         => 'bool',
            'refundable'       => 'bool',
            'moderated_at'     => 'datetime',
            'anon'             => 'bool',
            'sticky'           => 'bool',
            'status'           => ModerationStatus::class,
            'personal_release' => 'bool',
        ];
    }

    /**
     * The attributes that should not be included in audit log.
     *
     * @var string[]
     */
    protected $discarded = [
        'info_hash',
    ];

    /**
     * This query is to be added to a raw select from the torrents table.
     *
     * The fields it returns are used by Meilisearch to power the advanced
     * torrent search, quick search, RSS, and the API.
     */
    public const string SEARCHABLE = <<<'SQL'
            torrents.id,
            torrents.name,
            torrents.description,
            torrents.mediainfo,
            torrents.bdinfo,
            torrents.num_file,
            torrents.folder,
            torrents.size,
            torrents.leechers,
            torrents.seeders,
            torrents.times_completed,
            UNIX_TIMESTAMP(torrents.created_at) AS created_at,
            UNIX_TIMESTAMP(torrents.bumped_at) AS bumped_at,
            UNIX_TIMESTAMP(torrents.fl_until) AS fl_until,
            UNIX_TIMESTAMP(torrents.du_until) AS du_until,
            torrents.user_id,
            torrents.imdb,
            torrents.tvdb,
            torrents.tmdb_movie_id,
            torrents.tmdb_tv_id,
            torrents.mal,
            torrents.igdb,
            torrents.season_number,
            torrents.episode_number,
            torrents.free,
            torrents.doubleup,
            torrents.refundable,
            torrents.highspeed,
            torrents.status,
            torrents.anon,
            torrents.sticky,
            torrents.internal,
            UNIX_TIMESTAMP(torrents.deleted_at) AS deleted_at,
            torrents.distributor_id,
            torrents.region_id,
            torrents.personal_release,
            LOWER(HEX(torrents.info_hash)) AS info_hash,
            (
                SELECT COALESCE(JSON_ARRAYAGG(JSON_OBJECT(
                    'user_id', history.user_id
                )), JSON_ARRAY())
                FROM history
                WHERE torrents.id = history.torrent_id
                    AND seeder = 1
            ) AS json_history_seeders,
            (
                SELECT COALESCE(JSON_ARRAYAGG(JSON_OBJECT(
                    'user_id', history.user_id
                )), JSON_ARRAY())
                FROM history
                WHERE torrents.id = history.torrent_id
                    AND seeder = 0
            ) AS json_history_leechers,
            (
                SELECT COALESCE(JSON_ARRAYAGG(JSON_OBJECT(
                    'user_id', history.user_id
                )), JSON_ARRAY())
                FROM history
                WHERE torrents.id = history.torrent_id
                    AND active = 1
            ) AS json_history_active,
            (
                SELECT COALESCE(JSON_ARRAYAGG(JSON_OBJECT(
                    'user_id', history.user_id
                )), JSON_ARRAY())
                FROM history
                WHERE torrents.id = history.torrent_id
                    AND active = 0
            ) AS json_history_inactive,
            (
                SELECT COALESCE(JSON_ARRAYAGG(JSON_OBJECT(
                    'user_id', history.user_id
                )), JSON_ARRAY())
                FROM history
                WHERE torrents.id = history.torrent_id
                    AND completed_at IS NOT NULL
            ) AS json_history_complete,
            (
                SELECT COALESCE(JSON_ARRAYAGG(JSON_OBJECT(
                    'user_id', history.user_id
                )), JSON_ARRAY())
                FROM history
                WHERE torrents.id = history.torrent_id
                    AND completed_at IS NULL
            ) AS json_history_incomplete,
            (
                SELECT JSON_OBJECT(
                    'id', users.id,
                    'username', users.username,
                    'group', (
                        SELECT JSON_OBJECT(
                            'name', "groups".name,
                            'color', "groups".color,
                            'icon', "groups".icon,
                            'effect', "groups".effect
                        )
                        FROM "groups"
                        WHERE "groups".id = users.group_id
                        LIMIT 1
                    )
                )
                FROM users
                WHERE torrents.user_id = users.id
                LIMIT 1
            ) AS json_user,
            (
                SELECT JSON_OBJECT(
                    'id', categories.id,
                    'name', categories.name,
                    'image', categories.image,
                    'icon', categories.icon,
                    'no_meta', categories.no_meta != 0,
                    'music_meta', categories.music_meta != 0,
                    'game_meta', categories.game_meta != 0,
                    'tv_meta', categories.tv_meta != 0,
                    'movie_meta', categories.movie_meta != 0
                )
                FROM categories
                WHERE torrents.category_id = categories.id
                LIMIT 1
            ) AS json_category,
            (
                SELECT JSON_OBJECT(
                    'id', types.id,
                    'name', types.name
                )
                FROM types
                WHERE torrents.type_id = types.id
                LIMIT 1
            ) AS json_type,
            (
                SELECT JSON_OBJECT(
                    'id', resolutions.id,
                    'name', resolutions.name
                )
                FROM resolutions
                WHERE torrents.resolution_id = resolutions.id
                LIMIT 1
            ) AS json_resolution,
            (
                SELECT vote_average
                FROM tmdb_movies
                WHERE
                    torrents.tmdb_movie_id = tmdb_movies.id
                    AND torrents.category_id in (
                        SELECT id
                        FROM categories
                        WHERE movie_meta = 1
                    )
                UNION
                SELECT vote_average
                FROM tmdb_tv
                WHERE
                    torrents.tmdb_tv_id = tmdb_tv.id
                    AND torrents.category_id in (
                        SELECT id
                        FROM categories
                        WHERE tv_meta = 1
                    )
                LIMIT 1
            ) AS rating,
            EXISTS(
                SELECT *
                FROM torrent_trumps
                WHERE torrents.id = torrent_trumps.torrent_id
            ) AS trumpable,
            EXISTS(
                SELECT *
                FROM featured_torrents
                WHERE torrents.id = featured_torrents.torrent_id
            ) AS featured,
            (
                SELECT JSON_OBJECT(
                    'id', tmdb_movies.id,
                    'name', tmdb_movies.title,
                    'year', YEAR(tmdb_movies.release_date),
                    'poster', tmdb_movies.poster,
                    'original_language', tmdb_movies.original_language,
                    'adult', tmdb_movies.adult != 0,
                    'rating', tmdb_movies.vote_average,
                    'companies', (
                        SELECT COALESCE(JSON_ARRAYAGG(JSON_OBJECT(
                            'id', tmdb_companies.id,
                            'name', tmdb_companies.name
                        )), JSON_ARRAY())
                        FROM tmdb_companies
                        WHERE tmdb_companies.id IN (
                            SELECT tmdb_company_id
                            FROM tmdb_company_tmdb_movie
                            WHERE tmdb_company_tmdb_movie.tmdb_movie_id = torrents.tmdb_movie_id
                        )
                    ),
                    'genres', (
                        SELECT COALESCE(JSON_ARRAYAGG(JSON_OBJECT(
                            'id', tmdb_genres.id,
                            'name', tmdb_genres.name
                        )), JSON_ARRAY())
                        FROM tmdb_genres
                        WHERE tmdb_genres.id IN (
                            SELECT tmdb_genre_id
                            FROM tmdb_genre_tmdb_movie
                            WHERE tmdb_genre_tmdb_movie.tmdb_movie_id = torrents.tmdb_movie_id
                        )
                    ),
                    'collection', (
                        SELECT JSON_OBJECT(
                            'id', tmdb_collection_tmdb_movie.tmdb_collection_id
                        )
                        FROM tmdb_collection_tmdb_movie
                        WHERE tmdb_movies.id = tmdb_collection_tmdb_movie.tmdb_movie_id
                        LIMIT 1
                    ),
                    'wishes', (
                        SELECT COALESCE(JSON_ARRAYAGG(JSON_OBJECT(
                            'user_id', wishes.user_id
                        )), JSON_ARRAY())
                        FROM wishes
                        WHERE wishes.tmdb_movie_id = tmdb_movies.id
                    )
                )
                FROM tmdb_movies
                WHERE torrents.tmdb_movie_id = tmdb_movies.id
                    AND torrents.category_id in (
                        SELECT id
                        FROM categories
                        WHERE movie_meta = 1
                    )
                LIMIT 1
            ) AS json_tmdb_movie,
            (
                SELECT JSON_OBJECT(
                    'id', tmdb_tv.id,
                    'name', tmdb_tv.name,
                    'year', YEAR(tmdb_tv.first_air_date),
                    'poster', tmdb_tv.poster,
                    'original_language', tmdb_tv.original_language,
                    'rating', tmdb_tv.vote_average,
                    'companies', (
                        SELECT COALESCE(JSON_ARRAYAGG(JSON_OBJECT(
                            'id', tmdb_companies.id,
                            'name', tmdb_companies.name
                        )), JSON_ARRAY())
                        FROM tmdb_companies
                        WHERE tmdb_companies.id IN (
                            SELECT tmdb_company_id
                            FROM tmdb_company_tmdb_tv
                            WHERE tmdb_company_tmdb_tv.tmdb_tv_id = torrents.tmdb_tv_id
                        )
                    ),
                    'genres', (
                        SELECT COALESCE(JSON_ARRAYAGG(JSON_OBJECT(
                            'id', tmdb_genres.id,
                            'name', tmdb_genres.name
                        )), JSON_ARRAY())
                        FROM tmdb_genres
                        WHERE tmdb_genres.id IN (
                            SELECT tmdb_genre_id
                            FROM tmdb_genre_tmdb_tv
                            WHERE tmdb_genre_tmdb_tv.tmdb_tv_id = torrents.tmdb_tv_id
                        )
                    ),
                    'networks', (
                        SELECT COALESCE(JSON_ARRAYAGG(JSON_OBJECT(
                            'id', tmdb_networks.id,
                            'name', tmdb_networks.name
                        )), JSON_ARRAY())
                        FROM tmdb_networks
                        WHERE tmdb_networks.id IN (
                            SELECT tmdb_network_id
                            FROM tmdb_network_tmdb_tv
                            WHERE tmdb_network_tmdb_tv.tmdb_tv_id = torrents.tmdb_tv_id
                        )
                    ),
                    'wishes', (
                        SELECT COALESCE(JSON_ARRAYAGG(JSON_OBJECT(
                            'user_id', wishes.user_id
                        )), JSON_ARRAY())
                        FROM wishes
                        WHERE wishes.tmdb_tv_id = tmdb_tv.id
                    )
                )
                FROM tmdb_tv
                WHERE torrents.tmdb_tv_id = tmdb_tv.id
                    AND torrents.category_id in (
                        SELECT id
                        FROM categories
                        WHERE tv_meta = 1
                    )
                LIMIT 1
            ) AS json_tmdb_tv,
            (
                SELECT COALESCE(JSON_ARRAYAGG(JSON_OBJECT(
                    'id', playlist_torrents.playlist_id
                )), JSON_ARRAY())
                FROM playlist_torrents
                WHERE torrents.id = playlist_torrents.torrent_id
            ) AS json_playlists,
            (
                SELECT COALESCE(JSON_ARRAYAGG(JSON_OBJECT(
                    'user_id', freeleech_tokens.user_id
                )), JSON_ARRAY())
                FROM freeleech_tokens
                WHERE torrents.id = freeleech_tokens.torrent_id
            ) AS json_freeleech_tokens,
            (
                SELECT COALESCE(JSON_ARRAYAGG(JSON_OBJECT(
                    'user_id', bookmarks.user_id
                )), JSON_ARRAY())
                FROM bookmarks
                WHERE torrents.id = bookmarks.torrent_id
            ) AS json_bookmarks,
            (
                SELECT COALESCE(JSON_ARRAYAGG(JSON_OBJECT(
                    'id', files.id,
                    'name', files.name,
                    'size', files.size
                )), JSON_ARRAY())
                FROM files
                WHERE torrents.id = files.torrent_id
            ) AS json_files
    SQL;

    protected static function booted(): void
    {
        static::addGlobalScope(new ApprovedScope());
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
     * Belongs To A Distributor.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Distributor, $this>
     */
    public function distributor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Distributor::class);
    }

    /**
     * Belongs To A Region.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Region, $this>
     */
    public function region(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Region::class);
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
     * Belongs To A Playlist.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Playlist, $this>
     */
    public function playlists(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Playlist::class, 'playlist_torrents')->using(PlaylistTorrent::class)->withPivot('id');
    }

    /**
     * Torrent Has Been Moderated By.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, $this>
     */
    public function moderated(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by')->withDefault([
            'username' => 'System',
            'id'       => User::SYSTEM_USER_ID,
        ]);
    }

    /**
     * Has Many History.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<History, $this>
     */
    public function history(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(History::class);
    }

    /**
     * Has Many Tips.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<TorrentTip, $this>
     */
    public function tips(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TorrentTip::class);
    }

    /**
     * Has Many Thank.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Thank, $this>
     */
    public function thanks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Thank::class);
    }

    /**
     * Has Many HitRuns.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Warning, $this>
     */
    public function hitrun(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Warning::class, 'torrent');
    }

    /**
     * Has Many Featured.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<FeaturedTorrent, $this>
     */
    public function featured(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FeaturedTorrent::class);
    }

    /**
     * Has Many Files.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<TorrentFile, $this>
     */
    public function files(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TorrentFile::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Comment, $this>
     */
    public function comments(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Has Many Peers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Peer, $this>
     */
    public function peers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Peer::class);
    }

    /**
     * Has Many Seeds.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Peer, $this>
     */
    public function seeds(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Peer::class)->where('seeder', '=', true);
    }

    /**
     * Has Many Leeches.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Peer, $this>
     */
    public function leeches(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Peer::class)->where('seeder', '=', false);
    }

    /**
     * Has Many Subtitles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Subtitle, $this>
     */
    public function subtitles(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Subtitle::class);
    }

    /**
     * Relationship To Many Requests.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<TorrentRequest, $this>
     */
    public function requests(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TorrentRequest::class);
    }

    /**
     * Has many free leech tokens.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<FreeleechToken, $this>
     */
    public function freeleechTokens(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FreeleechToken::class);
    }

    /**
     * Bookmarks.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Bookmark, $this>
     */
    public function bookmarks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    /**
     * Resurrections.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Resurrection, $this>
     */
    public function resurrections(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Resurrection::class);
    }

    /**
     * Reports.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Report, $this>
     */
    public function reports(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Report::class);
    }

    /**
     * Trump.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<TorrentTrump, $this>
     */
    public function trump(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(TorrentTrump::class);
    }

    /**
     * Set The Torrents MediaInfo After Its Been Purified.
     */
    public function setMediaInfoAttribute(?string $value): void
    {
        $this->attributes['mediainfo'] = $value;
    }

    /**
     * Returns The Size In Human Format.
     */
    public function getSize(): string
    {
        $bytes = $this->size;

        return StringHelper::formatBytes($bytes, 2);
    }

    /**
     * Notify Uploader When An Action Is Taken.
     */
    public function notifyUploader(string $type, Thank|Comment $payload): bool
    {
        $user = User::with('notification')->findOrFail($this->user_id);

        switch (true) {
            case $payload instanceof Thank:
                if ($user->acceptsNotification(auth()->user(), $user, 'torrent', 'show_torrent_thank')) {
                    $user->notify(new NewThank('torrent', $payload));
                }

                break;
            case $payload instanceof Comment:
                if ($user->acceptsNotification(auth()->user(), $user, 'torrent', 'show_torrent_comment')) {
                    $user->notify(new NewComment($this, $payload));
                }

                break;
        }

        return true;
    }

    /**
     * Torrent Is Freeleech.
     */
    public function isFreeleech(?User $user = null): bool
    {
        $isFreeleech = $user && ($user->group->is_freeleech || cache()->get('personal_freeleech:'.$user->id));

        return $this->free || config('other.freeleech') || $isFreeleech;
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        $missingRequiredAttributes = array_diff([
            'id',
            'name',
            'description',
            'mediainfo',
            'bdinfo',
            'num_file',
            'folder',
            'size',
            'leechers',
            'seeders',
            'times_completed',
            'created_at',
            'bumped_at',
            'fl_until',
            'du_until',
            'user_id',
            'imdb',
            'tvdb',
            'tmdb_movie_id',
            'tmdb_tv_id',
            'mal',
            'igdb',
            'season_number',
            'episode_number',
            'free',
            'doubleup',
            'refundable',
            'highspeed',
            'featured',
            'status',
            'anon',
            'sticky',
            'internal',
            'deleted_at',
            'distributor_id',
            'region_id',
            'personal_release',
            'info_hash',
            'trumpable',
            'rating',
            'json_user',
            'json_type',
            'json_category',
            'json_resolution',
            'json_tmdb_movie',
            'json_tmdb_tv',
            'json_playlists',
            'json_freeleech_tokens',
            'json_bookmarks',
            'json_files',
            'json_history_seeders',
            'json_history_leechers',
            'json_history_active',
            'json_history_inactive',
            'json_history_complete',
            'json_history_incomplete',
        ], array_keys($this->getAttributes()));

        if ([] == $missingRequiredAttributes) {
            $torrent = $this;
        } else {
            // Refetch torrent if any required attributes are missing
            $torrent = Torrent::query()
                ->withoutGlobalScope(ApprovedScope::class)
                ->whereKey($this->id)
                ->selectRaw(self::SEARCHABLE)
                ->first();
        }

        return [
            'id'                 => $torrent->id,
            'name'               => $torrent->name,
            'description'        => $torrent->description,
            'mediainfo'          => $torrent->mediainfo,
            'bdinfo'             => $torrent->bdinfo,
            'num_file'           => $torrent->num_file,
            'folder'             => $torrent->folder,
            'size'               => $torrent->size,
            'leechers'           => $torrent->leechers,
            'seeders'            => $torrent->seeders,
            'times_completed'    => $torrent->times_completed,
            'created_at'         => $torrent->created_at?->timestamp,
            'bumped_at'          => $torrent->bumped_at?->timestamp,
            'fl_until'           => $torrent->fl_until?->timestamp,
            'du_until'           => $torrent->du_until?->timestamp,
            'user_id'            => $torrent->user_id,
            'imdb'               => $torrent->imdb,
            'tvdb'               => $torrent->tvdb,
            'tmdb_movie_id'      => $torrent->tmdb_movie_id,
            'tmdb_tv_id'         => $torrent->tmdb_tv_id,
            'mal'                => $torrent->mal,
            'igdb'               => $torrent->igdb,
            'season_number'      => $torrent->season_number,
            'episode_number'     => $torrent->episode_number,
            'free'               => $torrent->free,
            'doubleup'           => (bool) $torrent->doubleup,
            'refundable'         => (bool) $torrent->refundable,
            'highspeed'          => (bool) $torrent->highspeed,
            'featured'           => (bool) $torrent->featured,
            'status'             => $torrent->status->value,
            'anon'               => (bool) $torrent->anon,
            'sticky'             => (int) $torrent->sticky,
            'internal'           => (bool) $torrent->internal,
            'deleted_at'         => $torrent->deleted_at?->timestamp,
            'distributor_id'     => $torrent->distributor_id,
            'region_id'          => $torrent->region_id,
            'personal_release'   => (bool) $torrent->personal_release,
            'info_hash'          => bin2hex($torrent->info_hash),
            'rating'             => (float) $torrent->rating, /** @phpstan-ignore property.notFound (This property is selected in the query but doesn't exist on the model) */
            'trumpable'          => (bool) $torrent->trumpable, /** @phpstan-ignore property.notFound (This property is selected in the query but doesn't exist on the model) */
            'user'               => json_decode($torrent->json_user ?? 'null'),
            'type'               => json_decode($torrent->json_type ?? 'null'),
            'category'           => json_decode($torrent->json_category ?? 'null'),
            'resolution'         => json_decode($torrent->json_resolution ?? 'null'),
            'tmdb_movie'         => json_decode($torrent->json_tmdb_movie ?? 'null'),
            'tmdb_tv'            => json_decode($torrent->json_tmdb_tv ?? 'null'),
            'playlists'          => json_decode($torrent->json_playlists ?? '[]'),
            'freeleech_tokens'   => json_decode($torrent->json_freeleech_tokens ?? '[]'),
            'bookmarks'          => json_decode($torrent->json_bookmarks ?? '[]'),
            'files'              => json_decode($torrent->json_files ?? '[]'),
            'history_seeders'    => json_decode($torrent->json_history_seeders ?? '[]'),
            'history_leechers'   => json_decode($torrent->json_history_leechers ?? '[]'),
            'history_active'     => json_decode($torrent->json_history_active ?? '[]'),
            'history_inactive'   => json_decode($torrent->json_history_inactive ?? '[]'),
            'history_complete'   => json_decode($torrent->json_history_complete ?? '[]'),
            'history_incomplete' => json_decode($torrent->json_history_incomplete ?? '[]'),
        ];
    }

    /**
     * Modify the query used to retrieve models when making all of the models searchable.
     *
     * @param  Builder<self> $query
     * @return Builder<self>
     */
    protected function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->selectRaw(self::SEARCHABLE);
    }
}
