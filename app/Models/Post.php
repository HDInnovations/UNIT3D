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
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Post.
 *
 * @property int                             $id
 * @property string                          $content
 * @property bool                            $anon
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int                             $user_id
 * @property int                             $topic_id
 */
class Post extends Model
{
    use Auditable;

    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'content',
        'anon',
        'topic_id',
        'user_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array{anon: 'bool'}
     */
    protected function casts(): array
    {
        return [
            'anon' => 'bool',
        ];
    }

    /**
     * Get the topic that owns the post.
     *
     * @return BelongsTo<Topic, $this>
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * Get the user that wrote the post.
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
     * Get all likes for the post.
     *
     * @return HasMany<Like, $this>
     */
    public function likes(): HasMany
    {
        return $this->hasMany(Like::class)->where('like', '=', 1);
    }

    /**
     * Get all dislikes for the post.
     *
     * @return HasMany<Like, $this>
     */
    public function dislikes(): HasMany
    {
        return $this->hasMany(Like::class)->where('dislike', '=', 1);
    }

    /**
     * Get all tips for the post.
     *
     * @return HasMany<PostTip, $this>
     */
    public function tips(): HasMany
    {
        return $this->hasMany(PostTip::class);
    }

    /**
     * Get all the posts for the post's author.
     *
     * @return HasMany<Post, $this>
     */
    public function authorPosts(): HasMany
    {
        return $this->hasMany(Post::class, 'user_id', 'user_id');
    }

    /**
     * Get all the topics for the post's author.
     *
     * @return HasMany<Topic, $this>
     */
    public function authorTopics(): HasMany
    {
        return $this->hasMany(Topic::class, 'first_post_user_id', 'user_id');
    }

    /**
     * Only include posts a user is authorized to.
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
        return $query->whereNotIn(
            'topic_id',
            Topic::query()
                ->whereRelation(
                    'forumPermissions',
                    fn ($query) => $query
                        ->where('group_id', '=', auth()->user()->group_id)
                        ->where(
                            fn ($query) => $query
                                ->whereRaw('1 = 0')
                                ->when($canReadTopic !== null, fn ($query) => $query->orWhere('read_topic', '!=', $canReadTopic))
                                ->when($canReplyTopic !== null, fn ($query) => $query->orWhere('reply_topic', '!=', $canReplyTopic))
                                ->when($canStartTopic !== null, fn ($query) => $query->orWhere('start_topic', '!=', $canStartTopic))
                        )
                )
                ->when($canReplyTopic && !auth()->user()->group->is_modo, fn ($query) => $query->where('state', '=', 'open'))
                ->select('id')
        );
    }
}
