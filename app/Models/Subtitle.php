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
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Helpers\StringHelper;
use App\Models\Scopes\ApprovedScope;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Subtitle.
 *
 * @property int                             $id
 * @property string                          $title
 * @property string                          $file_name
 * @property int                             $file_size
 * @property int                             $language_id
 * @property string                          $extension
 * @property string|null                     $note
 * @property int|null                        $downloads
 * @property int                             $verified
 * @property int                             $user_id
 * @property int                             $torrent_id
 * @property bool                            $anon
 * @property ModerationStatus                $status
 * @property \Illuminate\Support\Carbon|null $moderated_at
 * @property int|null                        $moderated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Subtitle extends Model
{
    use Auditable;

    /** @use HasFactory<\Database\Factories\SubtitleFactory> */
    use HasFactory;

    protected $guarded = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array{anon: 'bool', status: class-string<ModerationStatus>, moderated_at: 'datetime'}
     */
    protected function casts(): array
    {
        return [
            'anon'         => 'bool',
            'status'       => ModerationStatus::class,
            'moderated_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new ApprovedScope());
    }

    /**
     * Get the user that uploaded the subtitle.
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
     * Get the torrent associated with the subtitle.
     *
     * @return BelongsTo<Torrent, $this>
     */
    public function torrent(): BelongsTo
    {
        return $this->belongsTo(Torrent::class);
    }

    /**
     * Get the language associated with the subtitle.
     *
     * @return BelongsTo<MediaLanguage, $this>
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(MediaLanguage::class);
    }

    /**
     * Gets the size in human format.
     */
    public function getSize(): string
    {
        $bytes = $this->file_size;

        return StringHelper::formatBytes($bytes, 2);
    }
}
