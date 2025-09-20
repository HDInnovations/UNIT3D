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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Subscription.
 *
 * @property int                             $id
 * @property int                             $user_id
 * @property int|null                        $forum_id
 * @property int|null                        $topic_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Subscription extends Model
{
    use Auditable;

    /** @use HasFactory<\Database\Factories\SubscriptionFactory> */
    use HasFactory;

    /**
     * Get the user that owns the subscription.
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
     * Gets the subscribed topic.
     *
     * @return BelongsTo<Topic, $this>
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * Gets the subscribed forum.
     *
     * @return BelongsTo<Forum, $this>
     */
    public function forum(): BelongsTo
    {
        return $this->belongsTo(Forum::class);
    }

    /**
     * Scope query to only include subscriptions of a forum.
     *
     * @param  Builder<Subscription> $query
     * @return Builder<Subscription>
     */
    public function scopeOfForum(Builder $query, int $forum_id): Builder
    {
        return $query->where('forum_id', '=', $forum_id);
    }

    /**
     * Scope query to only include subscriptions of a topic.
     *
     * @param  Builder<Subscription> $query
     * @return Builder<Subscription>
     */
    public function scopeOfTopic($query, int $topic_id): Builder
    {
        return $query->where('topic_id', '=', $topic_id);
    }
}
