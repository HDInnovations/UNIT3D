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

use App\Helpers\StringHelper;
use App\Traits\UsersOnlineTrait;
use Assada\Achievements\Achiever;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

/**
 * App\Models\User.
 *
 * @property int                             $id
 * @property string                          $username
 * @property string                          $email
 * @property string                          $password
 * @property string|null                     $two_factor_secret
 * @property string|null                     $two_factor_recovery_codes
 * @property \Illuminate\Support\Carbon|null $two_factor_confirmed_at
 * @property string                          $passkey
 * @property int                             $group_id
 * @property int                             $uploaded
 * @property int                             $downloaded
 * @property string|null                     $image
 * @property string|null                     $title
 * @property string|null                     $about
 * @property string|null                     $signature
 * @property int                             $fl_tokens
 * @property string                          $seedbonus
 * @property int                             $invites
 * @property int                             $hitandruns
 * @property string                          $rsskey
 * @property int                             $chatroom_id
 * @property int                             $read_rules
 * @property bool                            $can_chat
 * @property bool                            $can_comment
 * @property bool                            $can_download
 * @property bool                            $can_request
 * @property bool                            $can_invite
 * @property bool                            $can_upload
 * @property bool                            $is_donor
 * @property bool                            $is_lifetime
 * @property string|null                     $remember_token
 * @property string|null                     $api_token
 * @property \Illuminate\Support\Carbon|null $last_login
 * @property \Illuminate\Support\Carbon|null $last_action
 * @property \Illuminate\Support\Carbon|null $disabled_at
 * @property int|null                        $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int                             $chat_status_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int                             $own_flushes
 * @property string|null                     $email_verified_at
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use Achiever;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use TwoFactorAuthenticatable;
    use UsersOnlineTrait;

    /**
     * The Attributes Excluded From The Model's JSON Form.
     *
     * @var list<string>
     */
    protected $hidden = [
        'email',
        'password',
        'passkey',
        'rsskey',
        'remember_token',
        'api_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array{
     *     last_login: 'datetime',
     *     last_action: 'datetime',
     *     disabled_at: 'datetime',
     *     can_comment: 'bool',
     *     can_download: 'bool',
     *     can_request: 'bool',
     *     can_invite: 'bool',
     *     can_upload: 'bool',
     *     can_chat: 'bool',
     *     seedbonus: 'decimal:2',
     *     is_donor: 'bool',
     *     is_lifetime: 'bool'
     * }
     */
    protected function casts(): array
    {
        return [
            'seedbonus'               => 'decimal:2',
            'last_login'              => 'datetime',
            'last_action'             => 'datetime',
            'disabled_at'             => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
            'can_comment'             => 'bool',
            'can_download'            => 'bool',
            'can_request'             => 'bool',
            'can_invite'              => 'bool',
            'can_upload'              => 'bool',
            'can_chat'                => 'bool',
            'is_donor'                => 'bool',
            'is_lifetime'             => 'bool',
        ];
    }

    /**
     * ID of the system user.
     */
    final public const int SYSTEM_USER_ID = 1;

    /**
     * Belongs To A Group.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Group, $this>
     */
    public function group(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Group::class)->withDefault([
            'color'        => config('user.group.defaults.color'),
            'effect'       => config('user.group.defaults.effect'),
            'icon'         => config('user.group.defaults.icon'),
            'name'         => config('user.group.defaults.name'),
            'slug'         => config('user.group.defaults.slug'),
            'position'     => config('user.group.defaults.position'),
            'is_admin'     => config('user.group.defaults.is_admin'),
            'is_freeleech' => config('user.group.defaults.is_freeleech'),
            'is_immune'    => config('user.group.defaults.is_immune'),
            'is_incognito' => config('user.group.defaults.is_incognito'),
            'is_internal'  => config('user.group.defaults.is_internal'),
            'is_modo'      => config('user.group.defaults.is_modo'),
            'is_trusted'   => config('user.group.defaults.is_trusted'),
            'can_upload'   => config('user.group.defaults.can_upload'),
            'level'        => config('user.group.defaults.level'),
        ]);
    }

    /**
     * Belongs To A Internal Group.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Internal, $this, InternalUser>
     */
    public function internals(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Internal::class)
            ->using(InternalUser::class)
            ->withPivot('id', 'position', 'created_at');
    }

    /**
     * Belongs To A Chatroom.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Chatroom, $this>
     */
    public function chatroom(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Chatroom::class);
    }

    /**
     * Belongs To A Chat Status.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<ChatStatus, $this>
     */
    public function chatStatus(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ChatStatus::class, 'chat_status_id', 'id');
    }

    /**
     * Belongs To Many Bookmarks.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Torrent, $this>
     */
    public function bookmarks(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Torrent::class, 'bookmarks', 'user_id', 'torrent_id')->withTimestamps();
    }

    /**
     * Belongs To Many Seeding Torrents.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Torrent, $this>
     */
    public function seedingTorrents(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Torrent::class, 'history')
            ->wherePivot('active', '=', 1)
            ->wherePivot('seeder', '=', 1);
    }

    /**
     * Belongs To Many Leeching Torrents.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Torrent, $this>
     */
    public function leechingTorrents(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Torrent::class, 'history')
            ->wherePivot('active', '=', 1)
            ->wherePivot('seeder', '=', 0);
    }

    /**
     * Belongs to many followers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<User, $this, \Illuminate\Database\Eloquent\Relations\Pivot, 'follow'>
     */
    public function followers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'target_id', 'user_id')
            ->as('follow')
            ->withTimestamps();
    }

    /**
     * Belongs to many connectable seeding torrents.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Torrent, $this>
     */
    public function connectableSeedingTorrents(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Torrent::class, 'peers')
            ->wherePivot('seeder', '=', 1)
            ->wherePivot('connectable', '=', true);
    }

    /**
     * Belongs to many followees.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<User, $this, \Illuminate\Database\Eloquent\Relations\Pivot, 'follow'>
     */
    public function following(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'user_id', 'target_id')
            ->as('follow')
            ->withTimestamps();
    }

    /**
     * Has Many Messages.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Message, $this>
     */
    public function messages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Has One Settings Object.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<UserSetting, $this>
     */
    public function settings(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(UserSetting::class)->withDefault([
            'censor'                            => false,
            'news_block_visible'                => true,
            'news_block_position'               => 0,
            'chat_block_visible'                => true,
            'chat_block_position'               => 1,
            'featured_block_visible'            => true,
            'featured_block_position'           => 2,
            'random_media_block_visible'        => true,
            'random_media_block_position'       => 3,
            'poll_block_visible'                => true,
            'poll_block_position'               => 4,
            'top_torrents_block_visible'        => true,
            'top_torrents_block_position'       => 5,
            'top_users_block_visible'           => true,
            'top_users_block_position'          => 6,
            'latest_topics_block_visible'       => true,
            'latest_topics_block_position'      => 7,
            'latest_posts_block_visible'        => true,
            'latest_posts_block_position'       => 8,
            'latest_comments_block_visible'     => true,
            'latest_comments_block_position'    => 9,
            'online_block_visible'              => true,
            'online_block_position'             => 10,
            'locale'                            => config('app.locale'),
            'style'                             => config('other.default_style', 0),
            'torrent_layout'                    => 0,
            'torrent_filters'                   => false,
            'custom_css'                        => null,
            'standalone_css'                    => null,
            'show_poster'                       => false,
            'unbookmark_torrents_on_completion' => false,
            'torrent_sort_field'                => 'bumped_at',
            'torrent_search_autofocus'          => false,
            'show_adult_content'                => true,
        ]);
    }

    /**
     * Get user's settings object.
     */
    public function getSettingsAttribute(): ?UserSetting
    {
        $settings = cache()->rememberForever('user-settings:by-user-id:'.$this->id, fn () => $this->getRelationValue('settings') ?? 'not found');

        if ($settings === 'not found') {
            $settings = null;
        }

        $this->setRelation('settings', $settings);

        return $settings;
    }

    /**
     * Has One Privacy Object.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<UserPrivacy, $this>
     */
    public function privacy(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(UserPrivacy::class);
    }

    /**
     * Get user's notification object.
     */
    public function getNotificationAttribute(): ?UserNotification
    {
        $notification = cache()->rememberForever('user-notification:by-user-id:'.$this->id, fn () => $this->getRelationValue('notification') ?? 'not found');

        if ($notification === 'not found') {
            $notification = null;
        }

        $this->setRelation('notification', $notification);

        return $notification;
    }

    /**
     * Has One Notifications Object.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<UserNotification, $this>
     */
    public function notification(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(UserNotification::class);
    }

    /**
     * Get user's privacy object.
     */
    public function getPrivacyAttribute(): ?UserPrivacy
    {
        $privacy = cache()->rememberForever('user-privacy:by-user-id:'.$this->id, fn () => $this->getRelationValue('privacy') ?? 'not found');

        if ($privacy === 'not found') {
            $privacy = null;
        }

        $this->setRelation('privacy', $privacy);

        return $privacy;
    }

    /**
     * Has One Watchlist Object.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<Watchlist, $this>
     */
    public function watchlist(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Watchlist::class);
    }

    /**
     * Has Many RSS Feeds.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Rss, $this>
     */
    public function rss(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Rss::class);
    }

    /**
     * Has Many Echo Settings.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<UserEcho, $this>
     */
    public function echoes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserEcho::class);
    }

    /**
     * Has Many Audible Settings.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<UserAudible, $this>
     */
    public function audibles(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserAudible::class);
    }

    /**
     * Has Many Thanks Given.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Thank, $this>
     */
    public function thanksGiven(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Thank::class, 'user_id', 'id');
    }

    /**
     * Has Many Wish's.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Wish, $this>
     */
    public function wishes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Wish::class);
    }

    /**
     * Has Many Thanks Received.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough<Thank, Torrent, $this>
     */
    public function thanksReceived(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(Thank::class, Torrent::class);
    }

    /**
     * Has Many Polls.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Poll, $this>
     */
    public function polls(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Poll::class);
    }

    /**
     * Has Many Torrents.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Torrent, $this>
     */
    public function torrents(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Torrent::class);
    }

    /**
     * Has Many Playlist.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Playlist, $this>
     */
    public function playlists(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Playlist::class);
    }

    /**
     * Has Many Playlist Suggestions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<PlaylistSuggestion, $this>
     */
    public function playlistSuggestions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PlaylistSuggestion::class);
    }

    /**
     * Has Many Sent PM's.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<PrivateMessage, $this>
     */
    public function sentPrivateMessages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PrivateMessage::class, 'sender_id');
    }

    /**
     * Has Many Peers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Peer, $this>
     */
    public function peers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Peer::class);
    }

    /**
     * Has Many Articles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Article, $this>
     */
    public function articles(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Article::class);
    }

    /**
     * Has Many Topics.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Topic, $this>
     */
    public function topics(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Topic::class, 'first_post_user_id', 'id');
    }

    /**
     * Has Many Posts.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Post, $this>
     */
    public function posts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Has Many Comments.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Comment, $this>
     */
    public function comments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Has Many Torrent Requests.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<TorrentRequest, $this>
     */
    public function requests(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TorrentRequest::class);
    }

    /**
     * Has Approved Many Torrent Requests.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<TorrentRequest, $this>
     */
    public function ApprovedRequests(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TorrentRequest::class, 'approved_by');
    }

    /**
     * Has Filled Many Torrent Requests.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<TorrentRequest, $this>
     */
    public function filledRequests(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TorrentRequest::class, 'filled_by');
    }

    /**
     * Has Many Torrent Request BON Bounties.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<TorrentRequestBounty, $this>
     */
    public function requestBounty(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TorrentRequestBounty::class);
    }

    /**
     * Has Moderated Many Torrents.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Torrent, $this>
     */
    public function moderated(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Torrent::class, 'moderated_by');
    }

    /**
     * Has Many Notes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Note, $this>
     */
    public function notes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Note::class, 'user_id');
    }

    /**
     * Has Many Reports.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Report, $this>
     */
    public function reports(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    /**
     * Has Solved Many Reports.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Report, $this>
     */
    public function solvedReports(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Report::class, 'staff_id');
    }

    /**
     * Has Many Torrent History.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<History, $this>
     */
    public function history(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(History::class, 'user_id');
    }

    /**
     * Has Many Bans.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Ban, $this>
     */
    public function userban(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Ban::class, 'owned_by');
    }

    /**
     * Has Given Many Bans.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Ban, $this>
     */
    public function staffban(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Ban::class, 'created_by');
    }

    /**
     * Has Given Many Warnings.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Warning, $this>
     */
    public function staffwarning(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Warning::class, 'warned_by');
    }

    /**
     * Has Deleted Many Warnings.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Warning, $this>
     */
    public function staffdeletedwarning(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Warning::class, 'deleted_by');
    }

    /**
     * Has Many Warnings.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Warning, $this>
     */
    public function userwarning(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Warning::class, 'user_id');
    }

    /**
     * Has Given Many Invites.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Invite, $this>
     */
    public function sentInvites(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Invite::class, 'user_id');
    }

    /**
     * Has Received Many Invites.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Invite, $this>
     */
    public function receivedInvites(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Invite::class, 'accepted_by');
    }

    /**
     * Has Many Featured Torrents.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<FeaturedTorrent, $this>
     */
    public function featuredTorrent(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FeaturedTorrent::class);
    }

    /**
     * Has Many Post Likes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Like, $this>
     */
    public function likes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Like::class);
    }

    /**
     * Has Many Subscriptions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Subscription, $this>
     */
    public function subscriptions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Has Many Resurrections.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Resurrection, $this>
     */
    public function resurrections(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Resurrection::class);
    }

    /**
     * Has Many Subscribed topics.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Forum, $this>
     */
    public function subscribedForums(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Forum::class, 'subscriptions');
    }

    /**
     * Has Many Subscribed topics.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Topic, $this>
     */
    public function subscribedTopics(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Topic::class, 'subscriptions');
    }

    /**
     * Has Many Permissions through Group.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<ForumPermission, $this>
     */
    public function forumPermissions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ForumPermission::class, 'group_id', 'group_id');
    }

    /**
     * Has many free leech tokens.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<FreeleechToken, $this>
     */
    public function freeleechTokens(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FreeleechToken::class);
    }

    /**
     * Has many warnings.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Warning, $this>
     */
    public function warnings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Warning::class);
    }

    /**
     * Has Many Tickets.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Ticket, $this>
     */
    public function tickets(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Ticket::class, 'user_id');
    }

    /**
     * Has Many Personal Freeleeches.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<PersonalFreeleech, $this>
     */
    public function personalFreeleeches(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PersonalFreeleech::class);
    }

    /**
     * Has many failed logins.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<FailedLoginAttempt, $this>
     */
    public function failedLogins(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FailedLoginAttempt::class);
    }

    /**
     * Has many upload snatches.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough<History, Torrent, $this>
     */
    public function uploadSnatches(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(History::class, Torrent::class)->whereNotNull('completed_at');
    }

    /**
     * Has many sent gifts.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Gift, $this>
     */
    public function sentGifts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Gift::class, 'sender_id');
    }

    /**
     * Has many received gifts.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Gift, $this>
     */
    public function receivedGifts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Gift::class, 'recipient_id');
    }

    /**
     * Has many sent tips.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<PostTip, $this>
     */
    public function sentPostTips(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PostTip::class, 'sender_id');
    }

    /**
     * Has many received tips.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<PostTip, $this>
     */
    public function receivedPostTips(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PostTip::class, 'recipient_id');
    }

    /**
     * Has many sent tips.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<TorrentTip, $this>
     */
    public function sentTorrentTips(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TorrentTip::class, 'sender_id');
    }

    /**
     * Has many received tips.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<TorrentTip, $this>
     */
    public function receivedTorrentTips(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TorrentTip::class, 'recipient_id');
    }

    /**
     * Has many seedboxes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Seedbox, $this>
     */
    public function seedboxes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Seedbox::class);
    }

    /**
     * Has one application.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough<Application, Invite, $this>
     */
    public function application(): \Illuminate\Database\Eloquent\Relations\HasOneThrough
    {
        return $this->hasOneThrough(Application::class, Invite::class, 'accepted_by', 'email', 'id', 'email');
    }

    /**
     * Has many passkeys.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Passkey, $this>
     */
    public function passkeys(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Passkey::class);
    }

    /**
     * Has many rsskeys.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Rsskey, $this>
     */
    public function rsskeys(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Rsskey::class);
    }

    /**
     * Has many apikeys.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Apikey, $this>
     */
    public function apikeys(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Apikey::class);
    }

    /**
     * Has many email updates.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<EmailUpdate, $this>
     */
    public function emailUpdates(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EmailUpdate::class);
    }

    /**
     * Has many password reset histories.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<PasswordResetHistory, $this>
     */
    public function passwordResetHistories(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PasswordResetHistory::class);
    }

    /**
     * Has many torrent trumps.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<TorrentTrump, $this>
     */
    public function torrentTrumps(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TorrentTrump::class);
    }

    /**
     * Has Many Audits.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Audit, $this>
     */
    public function audits(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Audit::class);
    }

    /**
     * Has many claimed prizes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<ClaimedPrize, $this>
     */
    public function claimedPrizes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ClaimedPrize::class);
    }

    /**
     * Has many donations.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Donation, $this>
     */
    public function donations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Donation::class);
    }

    /**
     * Has many conversations.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Conversation, $this>
     */
    public function conversations(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Conversation::class, 'participants');
    }

    /**
     * Has many participants.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Participant, $this>
     */
    public function participations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Participant::class);
    }

    /**
     * Get the Users accepts notification as bool.
     */
    public function acceptsNotification(self $sender, self $target, string $group = 'follower', bool|string $type = false): bool
    {
        $targetGroup = 'json_'.$group.'_groups';

        if ($sender->id === $target->id) {
            return false;
        }

        if ($sender->group->is_modo || $sender->group->is_admin) {
            return true;
        }

        if ($target->notification?->block_notifications == 1) {
            return false;
        }

        if ($target->notification && $type && (!$target->notification->$type)) {
            return false;
        }

        if (\is_array($target->notification?->$targetGroup)) {
            return !\in_array($sender->group->id, $target->notification->$targetGroup, true);
        }

        return true;
    }

    /**
     * Get the Users allowed answer as bool.
     */
    public function isVisible(self $target, string $group = 'profile', bool|string $type = false): bool
    {
        $targetGroup = 'json_'.$group.'_groups';
        $sender = auth()->user();

        if ($sender->id == $target->id) {
            return true;
        }

        if ($sender->group->is_modo || $sender->group->is_admin) {
            return true;
        }

        if ($target->privacy?->getAttribute('hidden')) {
            return false;
        }

        if ($target->privacy && $type && (!$target->privacy->$type || $target->privacy->$type == 0)) {
            return false;
        }

        if (\is_array($target->privacy?->$targetGroup)) {
            return !\in_array($sender->group->id, $target->privacy->$targetGroup);
        }

        return true;
    }

    /**
     * Get the Users allowed answer as bool.
     */
    public function isAllowed(self $target, string $group = 'profile', bool|string $type = false): bool
    {
        $targetGroup = 'json_'.$group.'_groups';
        $sender = auth()->user();

        if ($sender->id == $target->id) {
            return true;
        }

        if ($sender->group->is_modo || $sender->group->is_admin) {
            return true;
        }

        if ($target->privacy?->private_profile == 1) {
            return false;
        }

        if ($target->privacy && $type && (!$target->privacy->$type || $target->privacy->$type == 0)) {
            return false;
        }

        if (\is_array($target->privacy?->$targetGroup)) {
            return !\in_array($sender->group->id, $target->privacy->$targetGroup);
        }

        return true;
    }

    /**
     * Return Upload In Human Format.
     */
    public function getFormattedUploadedAttribute(): string
    {
        $bytes = $this->uploaded;

        if ($bytes > 0) {
            return StringHelper::formatBytes((float) $bytes, 2);
        }

        return StringHelper::formatBytes(0, 2);
    }

    /**
     * Return Download In Human Format.
     */
    public function getFormattedDownloadedAttribute(): string
    {
        $bytes = $this->downloaded;

        if ($bytes > 0) {
            return StringHelper::formatBytes((float) $bytes, 2);
        }

        return StringHelper::formatBytes(0, 2);
    }

    /**
     * Return The Ratio.
     */
    public function getRatioAttribute(): float
    {
        if ($this->downloaded === 0) {
            return INF;
        }

        return round($this->uploaded / $this->downloaded, 2);
    }

    public function getFormattedRatioAttribute(): string
    {
        $ratio = $this->ratio;

        if (is_infinite($ratio)) {
            return '∞';
        }

        return (string) $ratio;
    }

    /**
     * Return the size (pretty formatted) which can be safely downloaded
     * without falling under the minimum ratio.
     */
    public function getFormattedBufferAttribute(): string
    {
        if (config('other.ratio') === 0) {
            return '∞';
        }

        $bytes = round(($this->uploaded / config('other.ratio')) - $this->downloaded);

        return StringHelper::formatBytes($bytes);
    }

    /**
     * Formats the seed bonus points of the User.
     */
    public function getFormattedSeedbonusAttribute(): string
    {
        return number_format((float) $this->seedbonus, 0, null, "\u{202F}");
    }

    /**
     * Make sure that password reset emails are sent after the user has sent a
     * password reset request, that way an attacker can't use the timing to
     * determine if an email was sent or not.
     *
     * @param       $token
     * @return void
     */
    public function sendPasswordResetNotification($token): void
    {
        dispatch(fn () => $this->notify(new ResetPassword($token)))->afterResponse();
    }
}
