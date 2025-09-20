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
use App\Models\Scopes\ApprovedScope;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Application.
 *
 * @property int                             $id
 * @property string                          $type
 * @property string                          $email
 * @property string|null                     $referrer
 * @property ModerationStatus                $status
 * @property \Illuminate\Support\Carbon|null $moderated_at
 * @property int|null                        $moderated_by
 * @property int|null                        $accepted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Application extends Model
{
    use Auditable;

    /** @use HasFactory<\Database\Factories\ApplicationFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array{moderated_at: 'datetime', status: class-string<ModerationStatus>}
     */
    protected function casts(): array
    {
        return [
            'moderated_at' => 'datetime',
            'status'       => ModerationStatus::class,
        ];
    }

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected static function booted(): void
    {
        static::addGlobalScope(new ApprovedScope());
    }

    /**
     * Get the user that owns the application.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user that moderated the application.
     *
     * @return BelongsTo<User, $this>
     */
    public function moderated(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    /**
     * Get the image proofs for the application.
     *
     * @return HasMany<ApplicationImageProof, $this>
     */
    public function imageProofs(): HasMany
    {
        return $this->hasMany(ApplicationImageProof::class);
    }

    /**
     * Get the URL proofs for the application.
     *
     * @return HasMany<ApplicationUrlProof, $this>
     */
    public function urlProofs(): HasMany
    {
        return $this->hasMany(ApplicationUrlProof::class);
    }
}
