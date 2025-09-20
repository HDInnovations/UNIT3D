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
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\Pivot;
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
     * The attributes that should be hidden for serialization.
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
     * Get the group associated with the user.
     *
     * @return BelongsTo<Group, $this>
     */
    public function group(): BelongsTo
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
     * Get the internal groups that the user belongs to.
     *
     * @return BelongsToMany<Internal, $this, InternalUser>
     */
    public function internals(): BelongsToMany
    {
        return $this->belongsToMany(Internal::class)
            ->using(InternalUser::class)
            ->withPivot('id', 'position', 'created_at');
    }

    /**
     * Get the chatroom that contains the user.
     *
     * @return BelongsTo<Chatroom, $this>
     */
    public function chatroom(): BelongsTo
    {
        return $this->belongsTo(Chatroom::class);
    }

    /**
     * Get the chat status associated with the user.
     *
     * @return BelongsTo<ChatStatus, $this>
     */
    public function chatStatus(): BelongsTo
    {
        return $this->belongsTo(ChatStatus::class, 'chat_status_id', 'id');
    }

    /**
     * Get the bookmarks that belong the user.
     *
     * @return BelongsToMany<Torrent, $this>
     */
    public function bookmarks(): BelongsToMany
    {
        return $this->belongsToMany(Torrent::class, 'bookmarks', 'user_id', 'torrent_id')->withTimestamps();
    }

    /**
     * Get the seeding torrents that belong to the user.
     *
     * @return BelongsToMany<Torrent, $this>
     */
    public function seedingTorrents(): BelongsToMany
    {
        return $this->belongsToMany(Torrent::class, 'history')
            ->wherePivot('active', '=', 1)
            ->wherePivot('seeder', '=', 1);
    }

    /**
     * Get the leeching torrents that belong to the user.
     *
     * @return BelongsToMany<Torrent, $this>
     */
    public function leechingTorrents(): BelongsToMany
    {
        return $this->belongsToMany(Torrent::class, 'history')
            ->wherePivot('active', '=', 1)
            ->wherePivot('seeder', '=', 0);
    }

    /**
     * Get the users that are following the user.
     *
     * @return BelongsToMany<User, $this, Pivot, 'follow'>
     */
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'target_id', 'user_id')
            ->as('follow')
            ->withTimestamps();
    }

    /**
     * Get the connectable seeding torrents that belong to the user.
     *
     * @return BelongsToMany<Torrent, $this>
     */
    public function connectableSeedingTorrents(): BelongsToMany
    {
        return $this->belongsToMany(Torrent::class, 'peers')
            ->wherePivot('seeder', '=', 1)
            ->wherePivot('connectable', '=', true);
    }

    /**
     * Get the users that the user is following.
     *
     * @return BelongsToMany<User, $this, Pivot, 'follow'>
     */
    public function following(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'user_id', 'target_id')
            ->as('follow')
            ->withTimestamps();
    }

    /**
     * Get the messages the user has sent.
     *
     * @return HasMany<Message, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get the settings associated with the user.
     *
     * @return HasOne<UserSetting, $this>
     */
    public function settings(): HasOne
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
     * Get the user's settings object.
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
     * Get the privacy settings associated with the user.
     *
     * @return HasOne<UserPrivacy, $this>
     */
    public function privacy(): HasOne
    {
        return $this->hasOne(UserPrivacy::class);
    }

    /**
     * Get the user's notification object.
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
     * Get the notification settings associated with the user.
     *
     * @return HasOne<UserNotification, $this>
     */
    public function notification(): HasOne
    {
        return $this->hasOne(UserNotification::class);
    }

    /**
     * Get the user's privacy object.
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
     * Get the watchlist associated with the user.
     *
     * @return HasOne<Watchlist, $this>
     */
    public function watchlist(): HasOne
    {
        return $this->hasOne(Watchlist::class);
    }

    /**
     * Get the RSS feeds the user owns.
     *
     * @return HasMany<Rss, $this>
     */
    public function rss(): HasMany
    {
        return $this->hasMany(Rss::class);
    }

    /**
     * Get the echo settings for the user.
     *
     * @return HasMany<UserEcho, $this>
     */
    public function echoes(): HasMany
    {
        return $this->hasMany(UserEcho::class);
    }

    /**
     * Get the audible settings for the user.
     *
     * @return HasMany<UserAudible, $this>
     */
    public function audibles(): HasMany
    {
        return $this->hasMany(UserAudible::class);
    }

    /**
     * Get the thanks given to torrents by the user.
     *
     * @return HasMany<Thank, $this>
     */
    public function thanksGiven(): HasMany
    {
        return $this->hasMany(Thank::class, 'user_id', 'id');
    }

    /**
     * Get the wishes for the user.
     *
     * @return HasMany<Wish, $this>
     */
    public function wishes(): HasMany
    {
        return $this->hasMany(Wish::class);
    }

    /**
     * Get the thanks received from torrents to the user.
     *
     * @return HasManyThrough<Thank, Torrent, $this>
     */
    public function thanksReceived(): HasManyThrough
    {
        return $this->hasManyThrough(Thank::class, Torrent::class);
    }

    /**
     * Get the polls created by the user.
     *
     * @return HasMany<Poll, $this>
     */
    public function polls(): HasMany
    {
        return $this->hasMany(Poll::class);
    }

    /**
     * Get the torrents uploaded by the user.
     *
     * @return HasMany<Torrent, $this>
     */
    public function torrents(): HasMany
    {
        return $this->hasMany(Torrent::class);
    }

    /**
     * Get the playlists created by the user.
     *
     * @return HasMany<Playlist, $this>
     */
    public function playlists(): HasMany
    {
        return $this->hasMany(Playlist::class);
    }

    /**
     * Get the playlist suggestions created by the user.
     *
     * @return HasMany<PlaylistSuggestion, $this>
     */
    public function playlistSuggestions(): HasMany
    {
        return $this->hasMany(PlaylistSuggestion::class);
    }

    /**
     * Get the private messages sent by the user.
     *
     * @return HasMany<PrivateMessage, $this>
     */
    public function sentPrivateMessages(): HasMany
    {
        return $this->hasMany(PrivateMessage::class, 'sender_id');
    }

    /**
     * Get the peers for the user.
     *
     * @return HasMany<Peer, $this>
     */
    public function peers(): HasMany
    {
        return $this->hasMany(Peer::class);
    }

    /**
     * Get the articles authored by the user.
     *
     * @return HasMany<Article, $this>
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    /**
     * Get the topics started by the user.
     *
     * @return HasMany<Topic, $this>
     */
    public function topics(): HasMany
    {
        return $this->hasMany(Topic::class, 'first_post_user_id', 'id');
    }

    /**
     * Get the posts written by the user.
     *
     * @return HasMany<Post, $this>
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get the comments written by the user.
     *
     * @return HasMany<Comment, $this>
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the torrent requests created by the user.
     *
     * @return HasMany<TorrentRequest, $this>
     */
    public function requests(): HasMany
    {
        return $this->hasMany(TorrentRequest::class);
    }

    /**
     * Get the torrent requests approved by the user.
     *
     * @return HasMany<TorrentRequest, $this>
     */
    public function ApprovedRequests(): HasMany
    {
        return $this->hasMany(TorrentRequest::class, 'approved_by');
    }

    /**
     * Get the torrent requests filled by the user.
     *
     * @return HasMany<TorrentRequest, $this>
     */
    public function filledRequests(): HasMany
    {
        return $this->hasMany(TorrentRequest::class, 'filled_by');
    }

    /**
     * Get the bounties added by the user.
     *
     * @return HasMany<TorrentRequestBounty, $this>
     */
    public function requestBounty(): HasMany
    {
        return $this->hasMany(TorrentRequestBounty::class);
    }

    /**
     * Get the torrents moderated by the user.
     *
     * @return HasMany<Torrent, $this>
     */
    public function moderated(): HasMany
    {
        return $this->hasMany(Torrent::class, 'moderated_by');
    }

    /**
     * Get the notes written by the user.
     *
     * @return HasMany<Note, $this>
     */
    public function notes(): HasMany
    {
        return $this->hasMany(Note::class, 'user_id');
    }

    /**
     * Get the reports reported by the user.
     *
     * @return HasMany<Report, $this>
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    /**
     * Get the reports solved by the user.
     *
     * @return HasMany<Report, $this>
     */
    public function solvedReports(): HasMany
    {
        return $this->hasMany(Report::class, 'staff_id');
    }

    /**
     * Get the torrent history associated with the user.
     *
     * @return HasMany<History, $this>
     */
    public function history(): HasMany
    {
        return $this->hasMany(History::class, 'user_id');
    }

    /**
     * Get the bans received by the user.
     *
     * @return HasMany<Ban, $this>
     */
    public function userban(): HasMany
    {
        return $this->hasMany(Ban::class, 'owned_by');
    }

    /**
     * Get the bans issued by the user.
     *
     * @return HasMany<Ban, $this>
     */
    public function staffban(): HasMany
    {
        return $this->hasMany(Ban::class, 'created_by');
    }

    /**
     * Get the warnings issues by the user.
     *
     * @return HasMany<Warning, $this>
     */
    public function staffwarning(): HasMany
    {
        return $this->hasMany(Warning::class, 'warned_by');
    }

    /**
     * Get the warnings deleted by the user.
     *
     * @return HasMany<Warning, $this>
     */
    public function staffdeletedwarning(): HasMany
    {
        return $this->hasMany(Warning::class, 'deleted_by');
    }

    /**
     * Get the warnings received by the user.
     *
     * @return HasMany<Warning, $this>
     */
    public function userwarning(): HasMany
    {
        return $this->hasMany(Warning::class, 'user_id');
    }

    /**
     * Get the invites sent by the user.
     *
     * @return HasMany<Invite, $this>
     */
    public function sentInvites(): HasMany
    {
        return $this->hasMany(Invite::class, 'user_id');
    }

    /**
     * Get the invites received by the user.
     *
     * @return HasMany<Invite, $this>
     */
    public function receivedInvites(): HasMany
    {
        return $this->hasMany(Invite::class, 'accepted_by');
    }

    /**
     * Get the torrents featured by the user.
     *
     * @return HasMany<FeaturedTorrent, $this>
     */
    public function featuredTorrent(): HasMany
    {
        return $this->hasMany(FeaturedTorrent::class);
    }

    /**
     * Get the likes created by the user.
     *
     * @return HasMany<Like, $this>
     */
    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    /**
     * Get the subscriptions for the user.
     *
     * @return HasMany<Subscription, $this>
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get the resurrections for the user.
     *
     * @return HasMany<Resurrection, $this>
     */
    public function resurrections(): HasMany
    {
        return $this->hasMany(Resurrection::class);
    }

    /**
     * Get the forums subscribed to by the user.
     *
     * @return BelongsToMany<Forum, $this>
     */
    public function subscribedForums(): BelongsToMany
    {
        return $this->belongsToMany(Forum::class, 'subscriptions');
    }

    /**
     * Get the topics subscribed to by the user.
     *
     * @return BelongsToMany<Topic, $this>
     */
    public function subscribedTopics(): BelongsToMany
    {
        return $this->belongsToMany(Topic::class, 'subscriptions');
    }

    /**
     * Get the forum permissions of the user's group.
     *
     * @return HasMany<ForumPermission, $this>
     */
    public function forumPermissions(): HasMany
    {
        return $this->hasMany(ForumPermission::class, 'group_id', 'group_id');
    }

    /**
     * Get the the freeleech tokens for the user.
     *
     * @return HasMany<FreeleechToken, $this>
     */
    public function freeleechTokens(): HasMany
    {
        return $this->hasMany(FreeleechToken::class);
    }

    /**
     * Get the warnings for the user.
     *
     * @return HasMany<Warning, $this>
     */
    public function warnings(): HasMany
    {
        return $this->hasMany(Warning::class);
    }

    /**
     * Get the tickets for the user.
     *
     * @return HasMany<Ticket, $this>
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'user_id');
    }

    /**
     * Get the personal freeleeches for the user.
     *
     * @return HasMany<PersonalFreeleech, $this>
     */
    public function personalFreeleeches(): HasMany
    {
        return $this->hasMany(PersonalFreeleech::class);
    }

    /**
     * Get the failed logins for the user.
     *
     * @return HasMany<FailedLoginAttempt, $this>
     */
    public function failedLogins(): HasMany
    {
        return $this->hasMany(FailedLoginAttempt::class);
    }

    /**
     * Get the upload snatches for the user.
     *
     * @return HasManyThrough<History, Torrent, $this>
     */
    public function uploadSnatches(): HasManyThrough
    {
        return $this->hasManyThrough(History::class, Torrent::class)->whereNotNull('completed_at');
    }

    /**
     * Get the gifts sent by the user.
     *
     * @return HasMany<Gift, $this>
     */
    public function sentGifts(): HasMany
    {
        return $this->hasMany(Gift::class, 'sender_id');
    }

    /**
     * Get the gifts received by the user.
     *
     * @return HasMany<Gift, $this>
     */
    public function receivedGifts(): HasMany
    {
        return $this->hasMany(Gift::class, 'recipient_id');
    }

    /**
     * Get the tips sent by the user.
     *
     * @return HasMany<PostTip, $this>
     */
    public function sentPostTips(): HasMany
    {
        return $this->hasMany(PostTip::class, 'sender_id');
    }

    /**
     * Get the post tips received by the user.
     *
     * @return HasMany<PostTip, $this>
     */
    public function receivedPostTips(): HasMany
    {
        return $this->hasMany(PostTip::class, 'recipient_id');
    }

    /**
     * Get the torrent tips sent by the user.
     *
     * @return HasMany<TorrentTip, $this>
     */
    public function sentTorrentTips(): HasMany
    {
        return $this->hasMany(TorrentTip::class, 'sender_id');
    }

    /**
     * Get the torrent tips received by the user.
     *
     * @return HasMany<TorrentTip, $this>
     */
    public function receivedTorrentTips(): HasMany
    {
        return $this->hasMany(TorrentTip::class, 'recipient_id');
    }

    /**
     * Get the seedboxes owned by the user.
     *
     * @return HasMany<Seedbox, $this>
     */
    public function seedboxes(): HasMany
    {
        return $this->hasMany(Seedbox::class);
    }

    /**
     * Get the application submitted by the user.
     *
     * @return HasOneThrough<Application, Invite, $this>
     */
    public function application(): HasOneThrough
    {
        return $this->hasOneThrough(Application::class, Invite::class, 'accepted_by', 'email', 'id', 'email');
    }

    /**
     * Get passkeys for the user.
     *
     * @return HasMany<Passkey, $this>
     */
    public function passkeys(): HasMany
    {
        return $this->hasMany(Passkey::class);
    }

    /**
     * Get the rsskeys for the user.
     *
     * @return HasMany<Rsskey, $this>
     */
    public function rsskeys(): HasMany
    {
        return $this->hasMany(Rsskey::class);
    }

    /**
     * Get the apikeys for the user.
     *
     * @return HasMany<Apikey, $this>
     */
    public function apikeys(): HasMany
    {
        return $this->hasMany(Apikey::class);
    }

    /**
     * Get the email updates for the user.
     *
     * @return HasMany<EmailUpdate, $this>
     */
    public function emailUpdates(): HasMany
    {
        return $this->hasMany(EmailUpdate::class);
    }

    /**
     * Get the password reset history for the user.
     *
     * @return HasMany<PasswordResetHistory, $this>
     */
    public function passwordResetHistories(): HasMany
    {
        return $this->hasMany(PasswordResetHistory::class);
    }

    /**
     * Get the torrent trumps for the user.
     *
     * @return HasMany<TorrentTrump, $this>
     */
    public function torrentTrumps(): HasMany
    {
        return $this->hasMany(TorrentTrump::class);
    }

    /**
     * Get the audits created by the user.
     *
     * @return HasMany<Audit, $this>
     */
    public function audits(): HasMany
    {
        return $this->hasMany(Audit::class);
    }

    /**
     * Get the prizes claimed by the user.
     *
     * @return HasMany<ClaimedPrize, $this>
     */
    public function claimedPrizes(): HasMany
    {
        return $this->hasMany(ClaimedPrize::class);
    }

    /**
     * Get the donations submitted by the user.
     *
     * @return HasMany<Donation, $this>
     */
    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    /**
     * Get the conversations participated in by the user.
     *
     * @return BelongsToMany<Conversation, $this>
     */
    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(Conversation::class, 'participants');
    }

    /**
     * Get the participations of the user in conversations.
     *
     * @return HasMany<Participant, $this>
     */
    public function participations(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    /**
     * Get the user's notification acceptance as bool.
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
     * Get the user's privacy hidden as bool.
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
     * Get the user's privacy visibility as bool.
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
     * Get upload in human format.
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
     * Get download in human format.
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
     * Get the ratio.
     */
    public function getRatioAttribute(): float
    {
        if ($this->downloaded === 0) {
            return INF;
        }

        return round($this->uploaded / $this->downloaded, 2);
    }

    /**
     * Get ratio in human format.
     */
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
     * Get the formatted bonus points of the user.
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
