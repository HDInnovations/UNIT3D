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
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\Topic.
 *
 * @property int                             $id
 * @property string                          $name
 * @property string|null                     $state
 * @property int                             $priority
 * @property bool                            $approved
 * @property bool                            $denied
 * @property bool                            $solved
 * @property bool                            $invalid
 * @property bool                            $bug
 * @property bool                            $suggestion
 * @property bool                            $implemented
 * @property int|null                        $num_post
 * @property int|null                        $first_post_user_id
 * @property int|null                        $last_post_id
 * @property int|null                        $last_post_user_id
 * @property \Illuminate\Support\Carbon|null $last_post_created_at
 * @property int|null                        $views
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int                             $forum_id
 */
class Topic extends Model
{
    use Auditable;

    /** @use HasFactory<\Database\Factories\TopicFactory> */
    use HasFactory;

    protected $guarded = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array{last_post_created_at: 'datetime', priority: 'integer', approved: 'bool', denied: 'bool', solved: 'bool', invalid: 'bool', bug: 'bool', suggestion: 'bool', implemented: 'bool'}
     */
    protected function casts(): array
    {
        return [
            'last_post_created_at' => 'datetime',
            'priority'             => 'integer',
            'approved'             => 'bool',
            'denied'               => 'bool',
            'solved'               => 'bool',
            'invalid'              => 'bool',
            'bug'                  => 'bool',
            'suggestion'           => 'bool',
            'implemented'          => 'bool',
        ];
    }

    /**
     * Get the forum that owns the topic.
     *
     * @return BelongsTo<Forum, $this>
     */
    public function forum(): BelongsTo
    {
        return $this->belongsTo(Forum::class);
    }

    /**
     * Get the user who started the topic.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'first_post_user_id', 'id');
    }

    /**
     * Get the posts for the topic.
     *
     * @return HasMany<Post, $this>
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get the reads of the topic.
     *
     * @return HasMany<TopicRead, $this>
     */
    public function reads(): HasMany
    {
        return $this->hasMany(TopicRead::class);
    }

    /**
     * Get the subscriptions of the topic.
     *
     * @return HasMany<Subscription, $this>
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get the forum permissions of the topic.
     *
     * @return HasMany<ForumPermission, $this>
     */
    public function forumPermissions(): HasMany
    {
        return $this->hasMany(ForumPermission::class, 'forum_id', 'forum_id');
    }

    /**
     * Get the users subscribed to the topic.
     *
     * @return BelongsToMany<User, $this>
     */
    public function subscribedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, Subscription::class);
    }

    /**
     * Get the latest post for the topic.
     *
     * @return HasOne<Post, $this>
     */
    public function latestPostSlow(): HasOne
    {
        return $this->hasOne(Post::class)->latestOfMany();
    }

    /**
     * Get the latest post for the topic (cached).
     *
     * @return BelongsTo<Post, $this>
     */
    public function latestPost(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'last_post_id');
    }

    /**
     * Get the latest poster for the topic (cached).
     *
     * @return BelongsTo<User, $this>
     */
    public function latestPoster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_post_user_id');
    }

    /**
     * Scope query to only include topics a user is authorized to.
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
                'forumPermissions',
                fn ($query) => $query
                    ->where('group_id', '=', auth()->user()->group_id)
                    ->when($canReadTopic !== null, fn ($query) => $query->where('read_topic', '=', $canReadTopic))
                    ->when($canReplyTopic !== null, fn ($query) => $query->where('reply_topic', '=', $canReplyTopic))
                    ->when($canStartTopic !== null, fn ($query) => $query->where('start_topic', '=', $canStartTopic))
            )
            ->when($canReplyTopic && !auth()->user()->group->is_modo, fn ($query) => $query->where('state', '=', 'open'));
    }
}
