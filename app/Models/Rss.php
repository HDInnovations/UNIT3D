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
use Illuminate\Database\Eloquent\SoftDeletes;
use stdClass;

/**
 * App\Models\Rss.
 *
 * @property int                             $id
 * @property int                             $position
 * @property string                          $name
 * @property int                             $user_id
 * @property bool                            $is_private
 * @property int                             $is_torrent
 * @property array                           $json_torrent
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Rss extends Model
{
    use Auditable;

    /** @use HasFactory<\Database\Factories\RssFactory> */
    use HasFactory;
    use SoftDeletes;

    /**
     * The table associated with the mode.
     *
     * @var string
     */
    protected $table = 'rss';

    /**
     * Get the attributes that should be cast.
     *
     * @return array{name: 'string', json_torrent: 'array', expected_fields: 'array', is_private: 'bool'}
     */
    protected function casts(): array
    {
        return [
            'name'            => 'string',
            'json_torrent'    => 'array',
            'expected_fields' => 'array',
            'is_private'      => 'bool',
        ];
    }

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Get the user that owns the rss feed.
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
     * Get the staff user that created the rss feed.
     *
     * @return BelongsTo<User, $this>
     */
    public function staff(): BelongsTo
    {
        // Not needed yet. Just added for future extendability.
        return $this->belongsTo(User::class, 'staff_id');
    }

    /**
     * Get the RSS feeds JSON Torrent as object.
     */
    public function getObjectTorrentAttribute(): stdClass|bool
    {
        // Went with attribute to avoid () calls in views. Uniform ->object_torrent vs ->json_torrent.
        if ($this->json_torrent) {
            $expected = $this->expected_fields;

            return (object) array_merge($expected, $this->json_torrent);
        }

        return false;
    }

    /**
     * Get the RSS feeds expected fields for form validation.
     *
     * @return array<string, null>
     */
    public function getExpectedFieldsAttribute(): array
    {
        // Just Torrents for now... extendable to check on feed type in future.
        return [
            'search'          => null,
            'description'     => null,
            'uploader'        => null,
            'imdb'            => null,
            'mal'             => null,
            'categories'      => null,
            'types'           => null,
            'resolutions'     => null,
            'genres'          => null,
            'freeleech'       => null,
            'doubleupload'    => null,
            'featured'        => null,
            'highspeed'       => null,
            'internal'        => null,
            'personalrelease' => null,
            'bookmark'        => null,
            'alive'           => null,
            'dying'           => null,
            'dead'            => null,
        ];
    }
}
