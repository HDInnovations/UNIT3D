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
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * App\Models\Playlist.
 *
 * @property int                             $id
 * @property int                             $playlist_category_id
 * @property int                             $user_id
 * @property string                          $name
 * @property string                          $description
 * @property string|null                     $cover_image
 * @property int|null                        $position
 * @property int                             $is_private
 * @property int                             $is_pinned
 * @property int                             $is_featured
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Playlist extends Model
{
    use Auditable;

    /** @use HasFactory<\Database\Factories\PlaylistFactory> */
    use HasFactory;

    protected $guarded = [];

    /**
     * Get the user that owns the playlist.
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
     * Get the category for the playlist.
     *
     * @return BelongsTo<PlaylistCategory, $this>
     */
    public function playlistCategory(): BelongsTo
    {
        return $this->belongsTo(PlaylistCategory::class);
    }

    /**
     * Get the torrents that belong to the playlist.
     *
     * @return BelongsToMany<Torrent, $this, PlaylistTorrent>
     */
    public function torrents(): BelongsToMany
    {
        return $this->belongsToMany(Torrent::class, 'playlist_torrents')->using(PlaylistTorrent::class)->withPivot('id')->withTimestamps();
    }

    /**
     * Get the suggestions for this playlist.
     *
     * @return HasMany<PlaylistSuggestion, $this>
     */
    public function suggestions(): HasMany
    {
        return $this->hasMany(PlaylistSuggestion::class);
    }

    /**
     * Get the comments for this playlist.
     *
     * @return MorphMany<Comment, $this>
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
