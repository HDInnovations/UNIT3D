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
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\UploadDraft.
 *
 * A private, owner-only, in-progress snapshot of the torrent upload create
 * form. Holds only a bounded allowlist of scalar/flag fields (never files,
 * passkeys, or provider raw payloads) so a user can save, list, restore, and
 * delete multiple named drafts before finally publishing via the normal
 * upload form. See UploadDraftController for the allowlist and access rules.
 *
 * @property int                             $id
 * @property int                             $user_id
 * @property string|null                     $title
 * @property array<string, mixed>            $fields
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
final class UploadDraft extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'fields',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fields' => 'array',
        ];
    }

    /**
     * Get the user that owns the draft.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
