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
 * @author     Obi-wana
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use AllowDynamicProperties;

/**
 * App\Models\UploadContestPrize.
 *
 * @property int                             $id
 * @property int                             $upload_contest_id
 * @property int                             $user_id
 * @property int                             $place_number
 * @property int                             $uploads
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
#[AllowDynamicProperties]
final class UploadContestWinner extends Model
{
    /** @use HasFactory<\Database\Factories\UploadContestWinnerFactory> */
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];

    /**
     * Get the UploadContest.
     *
     * @return BelongsTo<UploadContest, $this>
     */
    public function contest(): BelongsTo
    {
        return $this->belongsTo(UploadContest::class);
    }

    /**
     * Get the user.
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
}
