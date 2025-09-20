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
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\Forum.
 *
 * @property int                             $id
 * @property int|null                        $position
 * @property int|null                        $num_topic
 * @property int|null                        $num_post
 * @property int|null                        $last_topic_id
 * @property int|null                        $last_post_id
 * @property int|null                        $last_post_user_id
 * @property \Illuminate\Support\Carbon|null $last_post_created_at
 * @property string|null                     $name
 * @property string|null                     $slug
 * @property string|null                     $description
 * @property int                             $forum_category_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Forum extends Model
{
    use Auditable;

    /** @use HasFactory<\Database\Factories\ForumFactory> */
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]
     */
    protected $guarded = ['id', 'created_at'];

    /**
     * Get the topics for the forum.
     *
     * @return HasMany<Topic, $this>
     */
    public function topics(): HasMany
    {
        return $this->hasMany(Topic::class);
    }

    /**
     * Get the category for the forum.
     *
     * @return BelongsTo<ForumCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ForumCategory::class, 'forum_category_id');
    }

    /**
     * Get the posts for the forum.
     *
     * @return HasManyThrough<Post, Topic, $this>
     */
    public function posts(): HasManyThrough
    {
        return $this->hasManyThrough(Post::class, Topic::class);
    }

    /**
     * Get the forum's last replied topic.
     *
     * @return HasOne<Topic, $this>
     */
    public function lastRepliedTopicSlow(): HasOne
    {
        return $this->hasOne(Topic::class)->ofMany('last_post_created_at', 'max');
    }

    /**
     * Get the last replied topic of the forum (cached).
     *
     * @return BelongsTo<Topic, $this>
     */
    public function lastRepliedTopic(): BelongsTo
    {
        return $this->belongsTo(Topic::class, 'last_topic_id');
    }

    /**
     * Get the latest poster of the forum (cached).
     *
     * @return BelongsTo<User, $this>
     */
    public function latestPoster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_post_user_id');
    }

    /**
     * Get the subscriptions for the forum.
     *
     * @return HasMany<Subscription, $this>
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'forum_id', 'id');
    }

    /**
     * Get the permissions for the forum.
     *
     * @return HasMany<ForumPermission, $this>
     */
    public function permissions(): HasMany
    {
        return $this->hasMany(ForumPermission::class);
    }

    /**
     * Get the users that are subscribed to the forum.
     *
     * @return BelongsToMany<User, $this>
     */
    public function subscribedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, Subscription::class);
    }

    /**
     * Scope a query to only include forums a user is authorized to.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<self> $query
     * @return \Illuminate\Database\Eloquent\Builder<self>
     */
    public function scopeAuthorized(
        \Illuminate\Database\Eloquent\Builder $query,
        ?bool $canReadTopic = null,
        ?bool $canReplyTopic = null,
        ?bool $canStartTopic = null,
    ): \Illuminate\Database\Eloquent\Builder {
        return $query
            ->whereRelation(
                'permissions',
                fn ($query) => $query
                    ->where('group_id', '=', auth()->user()->group_id)
                    ->when($canReadTopic !== null, fn ($query) => $query->where('read_topic', '=', $canReadTopic))
                    ->when($canReplyTopic !== null, fn ($query) => $query->where('reply_topic', '=', $canReplyTopic))
                    ->when($canStartTopic !== null, fn ($query) => $query->where('start_topic', '=', $canStartTopic))
            );
    }
}
