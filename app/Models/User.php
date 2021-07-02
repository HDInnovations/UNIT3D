<?php
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

use App\Helpers\Bbcode;
use App\Helpers\Linkify;
use App\Helpers\StringHelper;
use App\Traits\HasPrivilege;
use App\Traits\HasRole;
use App\Traits\UsersOnlineTrait;
use Assada\Achievements\Achiever;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use voku\helper\AntiXSS;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use Achiever;
    use SoftDeletes;
    use UsersOnlineTrait;
    use HasPrivilege;
    use HasRole;

    /**
     * The Attributes Excluded From The Model's JSON Form.
     *
     * @var array
     */
    protected $hidden = [
        'email',
        'password',
        'passkey',
        'rsskey',
        'remember_token',
        'api_token',
    ];

    /**
     * The Attributes That Should Be Mutated To Dates.
     *
     * @var array
     */
    protected $dates = [
        'last_login',
        'last_action',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role', 'user_id', 'role_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function privileges()
    {
        return $this->belongsToMany(Role::class, 'user_privilege', 'user_id', 'privilege_id');
    }

    /**
     * Belongs To A Chatroom.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function chatroom()
    {
        return $this->belongsTo(Chatroom::class);
    }

    /**
     * Belongs To A Chat Status.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function chatStatus()
    {
        return $this->belongsTo(ChatStatus::class, 'chat_status_id', 'id');
    }

    /**
     * Belongs To Many Bookmarks.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function bookmarks()
    {
        return $this->belongsToMany(Torrent::class, 'bookmarks', 'user_id', 'torrent_id')->withTimestamps();
    }

    /**
     * @param $torrentId
     *
     * @return bool
     */
    public function isBookmarked($torrentId)
    {
        return $this->bookmarks()->where('torrent_id', '=', $torrentId)->first() !== null;
    }

    /**
     * Has Many Messages.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Has One Privacy Object.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function privacy()
    {
        return $this->hasOne(UserPrivacy::class);
    }

    /**
     * Has One Chat Object.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function chat()
    {
        return $this->hasOne(UserChat::class);
    }

    /**
     * Has One Notifications Object.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function notification()
    {
        return $this->hasOne(UserNotification::class);
    }

    /**
     * Has Many RSS Feeds.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rss()
    {
        return $this->hasMany(Rss::class);
    }

    /**
     * Has Many Echo Settings.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function echoes()
    {
        return $this->hasMany(UserEcho::class);
    }

    /**
     * Has Many Audible Settings.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function audibles()
    {
        return $this->hasMany(UserAudible::class);
    }

    /**
     * Has Many Thanks Given.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function thanksGiven()
    {
        return $this->hasMany(Thank::class, 'user_id', 'id');
    }

    /**
     * Has Many Wish's.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function wishes()
    {
        return $this->hasMany(Wish::class);
    }

    /**
     * Has Many Thanks Received.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function thanksReceived()
    {
        return $this->hasManyThrough(Thank::class, Torrent::class);
    }

    /**
     * Has Many Polls.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function polls()
    {
        return $this->hasMany(Poll::class);
    }

    /**
     * Has Many Torrents.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function torrents()
    {
        return $this->hasMany(Torrent::class);
    }

    /**
     * Has Many Playlist.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function playlists()
    {
        return $this->hasMany(Playlist::class);
    }

    /**
     * Has Many Sent PM's.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pm_sender()
    {
        return $this->hasMany(PrivateMessage::class, 'sender_id');
    }

    /**
     * Has Many Received PM's.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pm_receiver()
    {
        return $this->hasMany(PrivateMessage::class, 'receiver_id');
    }

    /**
     * Has Many Peers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function peers()
    {
        return $this->hasMany(Peer::class);
    }

    /**
     * Has Many Followers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function follows()
    {
        return $this->hasMany(Follow::class);
    }

    /**
     * Has Many Articles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    /**
     * Has Many Topics.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function topics()
    {
        return $this->hasMany(Topic::class, 'first_post_user_id', 'id');
    }

    /**
     * Has Many Posts.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Has Many Comments.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Has Many Torrent Requests.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function requests()
    {
        return $this->hasMany(TorrentRequest::class);
    }

    /**
     * Has Approved Many Torrent Requests.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ApprovedRequests()
    {
        return $this->hasMany(TorrentRequest::class, 'approved_by');
    }

    /**
     * Has Filled Many Torrent Requests.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function FilledRequests()
    {
        return $this->hasMany(TorrentRequest::class, 'filled_by');
    }

    /**
     * Has Many Torrent Request BON Bounties.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function requestBounty()
    {
        return $this->hasMany(TorrentRequestBounty::class);
    }

    /**
     * Has Moderated Many Torrents.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function moderated()
    {
        return $this->hasMany(Torrent::class, 'moderated_by');
    }

    /**
     * Has Many Notes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notes()
    {
        return $this->hasMany(Note::class, 'user_id');
    }

    /**
     * Has Many Reports.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reports()
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    /**
     * Has Solved Many Reports.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function solvedReports()
    {
        return $this->hasMany(Report::class, 'staff_id');
    }

    /**
     * Has Many Torrent History.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function history()
    {
        return $this->hasMany(History::class, 'user_id');
    }

    /**
     * Has Many Bans.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userban()
    {
        return $this->hasMany(Ban::class, 'owned_by');
    }

    /**
     * Has Given Many Bans.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function staffban()
    {
        return $this->hasMany(Ban::class, 'created_by');
    }

    /**
     * Has Given Many Warnings.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function staffwarning()
    {
        return $this->hasMany(Warning::class, 'warned_by');
    }

    /**
     * Has Deleted Many Warnings.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function staffdeletedwarning()
    {
        return $this->hasMany(Warning::class, 'deleted_by');
    }

    /**
     * Has Many Warnings.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userwarning()
    {
        return $this->hasMany(Warning::class, 'user_id');
    }

    /**
     * Has Given Many Invites.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sentInvite()
    {
        return $this->hasMany(Invite::class, 'user_id');
    }

    /**
     * Has Received Many Invites.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function receivedInvite()
    {
        return $this->hasMany(Invite::class, 'accepted_by');
    }

    /**
     * Has Many Featured Torrents.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function featuredTorrent()
    {
        return $this->hasMany(FeaturedTorrent::class);
    }

    /**
     * Has Many Post Likes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    /**
     * Has Given Many BON Tips.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bonGiven()
    {
        return $this->hasMany(BonTransactions::class, 'sender');
    }

    /**
     * Has Received Many BON Tips.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bonReceived()
    {
        return $this->hasMany(BonTransactions::class, 'receiver');
    }

    /**
     * Has Many Subscriptions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Has many free leech tokens.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function freeleechTokens()
    {
        return $this->hasMany(FreeleechToken::class);
    }

    /**
     * Has many warnings.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function warnings()
    {
        return $this->hasMany(Warning::class);
    }

    /**
     * Has Many Tickets.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'user_id');
    }

    /**
     * Get the Users username as slug.
     *
     * @return string
     */
    public function getSlugAttribute()
    {
        return Str::slug($this->username);
    }

    /**
     * Get the Users accepts notification as bool.
     *
     * @param string $group
     * @param bool   $type
     *
     * @return int
     */
    public function acceptsNotification(self $sender, self $target, $group = 'follower', $type = false)
    {
        $targetGroup = 'json_'.$group.'_groups';
        if ($sender->id === $target->id) {
            return false;
        }
        if ($sender->hasPrivilegeTo('users_bypass_notification_preferences')) {
            return true;
        }
        if ($target->block_notifications && $target->block_notifications == 1) {
            return false;
        }
        if ($target->notification && $type && (! $target->notification->$type)) {
            return false;
        }
        if ($target->notification && $target->notification->$targetGroup && \is_array($target->notification->$targetGroup['default_groups'])) {
            if (\array_key_exists($sender->group->id, $target->notification->$targetGroup['default_groups'])) {
                return $target->notification->$targetGroup['default_groups'][$sender->group->id] == 1;
            }

            return true;
        }

        return true;
    }

    /**
     * Get the Users allowed answer as bool.
     *
     * @param string $group
     * @param bool   $type
     *
     * @return int
     */
    public function isVisible(self $target, $group = 'profile', $type = false)
    {
        $targetGroup = 'json_'.$group.'_groups';
        $sender = \auth()->user();
        if ($sender->id == $target->id) {
            return true;
        }
        if ($sender->hasPrivilegeTo('users_view_private')) {
            return true;
        }
        if ($target->hidden && $target->hidden == 1) {
            return false;
        }
        if ($target->privacy && $type && (! $target->privacy->$type || $target->privacy->$type == 0)) {
            return false;
        }
        if ($target->privacy && $target->privacy->$targetGroup && \is_array($target->privacy->$targetGroup['default_groups'])) {
            if (\array_key_exists($sender->group->id, $target->privacy->$targetGroup['default_groups'])) {
                return $target->privacy->$targetGroup['default_groups'][$sender->group->id] == 1;
            }

            return true;
        }

        return true;
    }

    /**
     * Get the Users allowed answer as bool.
     *
     * @param string $group
     * @param bool   $type
     *
     * @return int
     */
    public function isAllowed(self $target, $group = 'profile', $type = false)
    {
        $targetGroup = 'json_'.$group.'_groups';
        $sender = \auth()->user();
        if ($sender->id == $target->id) {
            return true;
        }
        if ($sender->hasPrivilegeTo('users_view_private')) {
            return true;
        }
        if ($target->private_profile && $target->private_profile == 1) {
            return false;
        }
        if ($target->privacy && $type && (! $target->privacy->$type || $target->privacy->$type == 0)) {
            return false;
        }
        if ($target->privacy && $target->privacy->$targetGroup && \is_array($target->privacy->$targetGroup['default_groups'])) {
            if (\array_key_exists($sender->group->id, $target->privacy->$targetGroup['default_groups'])) {
                return $target->privacy->$targetGroup['default_groups'][$sender->group->id] == 1;
            }

            return true;
        }

        return true;
    }

    /**
     * Does Subscription Exist.
     *
     * @param $topicId
     *
     * @return string
     */
    public function isSubscribed(string $type, $topicId)
    {
        if ($type === 'topic') {
            return (bool) $this->subscriptions()->where('topic_id', '=', $topicId)->first(['id']);
        }

        return (bool) $this->subscriptions()->where('forum_id', '=', $topicId)->first(['id']);
    }

    /**
     * Get All Followers Of A User.
     *
     * @param $targetId
     *
     * @return string
     */
    public function isFollowing($targetId)
    {
        return (bool) $this->follows()->where('target_id', '=', $targetId)->first(['id']);
    }

    /**
     * Return Upload In Human Format.
     *
     * @param null $bytes
     * @param int  $precision
     *
     * @return string
     */
    public function getUploaded($bytes = null, $precision = 2)
    {
        $bytes = $this->uploaded;

        if ($bytes > 0) {
            return StringHelper::formatBytes((float) $bytes, 2);
        }

        return StringHelper::formatBytes(0, 2);
    }

    /**
     * Return Download In Human Format.
     *
     * @param null $bytes
     * @param int  $precision
     *
     * @return string
     */
    public function getDownloaded($bytes = null, $precision = 2)
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
    public function getRatio()
    {
        if ($this->downloaded === 0) {
            return INF;
        }

        return \round($this->uploaded / $this->downloaded, 2);
    }

    // Return the ratio pretty formated as a string.

    /**
     * @return string
     */
    public function getRatioString()
    {
        $ratio = $this->getRatio();
        if (\is_infinite($ratio)) {
            return '∞';
        }

        return (string) $ratio;
    }

    // Return the ratio after $size bytes would be downloaded.

    /**
     * @param $size
     *
     * @return float
     */
    public function ratioAfterSize($size)
    {
        if ($this->downloaded + $size == 0) {
            return INF;
        }

        return \round($this->uploaded / ($this->downloaded + $size), 2);
    }

    // Return the ratio after $size bytes would be downloaded, pretty formatted
    // as a string.

    /**
     * @param      $size
     * @param bool $freeleech
     *
     * @return string
     */
    public function ratioAfterSizeString($size, $freeleech = false)
    {
        if ($freeleech) {
            return $this->getRatioString().' ('.\trans('torrent.freeleech').')';
        }

        $ratio = $this->ratioAfterSize($size);
        if (\is_infinite($ratio)) {
            return '∞';
        }

        return (string) $ratio;
    }

    // Return the size (pretty formated) which can be safely downloaded
    // without falling under the minimum ratio.

    /**
     * @param $ratio
     *
     * @return string
     */
    public function untilRatio($ratio)
    {
        if ($ratio == 0.0) {
            return '∞';
        }

        $bytes = \round(($this->uploaded / $ratio) - $this->downloaded);

        return StringHelper::formatBytes($bytes);
    }

    /**
     * Set The Users Signature After Its Been Purified.
     *
     * @param string $value
     *
     * @return void
     */
    public function setSignatureAttribute($value)
    {
        $this->attributes['signature'] = \htmlspecialchars((new AntiXSS())->xss_clean($value), ENT_NOQUOTES);
    }

    /**
     * Returns the HTML of the user's signature.
     *
     * @return string html
     */
    public function getSignature()
    {
        $bbcode = new Bbcode();
        $linkify = new Linkify();

        return $linkify->linky($bbcode->parse($this->signature, true));
    }

    /**
     * Set The Users About Me After Its Been Purified.
     *
     * @param string $value
     *
     * @return void
     */
    public function setAboutAttribute($value)
    {
        $this->attributes['about'] = \htmlspecialchars((new AntiXSS())->xss_clean($value), ENT_NOQUOTES);
    }

    /**
     * Parse About Me And Return Valid HTML.
     *
     * @return string Parsed BBCODE To HTML
     */
    public function getAboutHtml()
    {
        if (empty($this->about)) {
            return 'N/A';
        }
        $bbcode = new Bbcode();
        $linkify = new Linkify();

        return $linkify->linky($bbcode->parse($this->about, true));
    }

    /**
     * @method getSeedbonus
     *
     * Formats the seebonus of the User
     *
     * @return decimal
     */
    public function getSeedbonus()
    {
        return \number_format($this->seedbonus, 0, '.', ' ');
    }

    /**
     * @method getSeeding
     *
     * Gets the amount of torrents a user seeds
     *
     * @return int
     */
    public function getSeeding()
    {
        return Peer::where('user_id', '=', $this->id)
            ->where('seeder', '=', '1')
            ->distinct('info_hash')
            ->count();
    }

    /**
     * @method getLast30Uploads
     *
     * Gets the amount of torrents a user seeds
     *
     * @return int
     */
    public function getLast30Uploads()
    {
        $current = Carbon::now();

        return Torrent::withAnyStatus()
            ->where('user_id', '=', $this->id)
            ->where('created_at', '>', $current->copy()->subDays(30)->toDateTimeString())
            ->count();
    }

    /**
     * @method getUploads
     *
     * Gets the amount of torrents a user seeds
     *
     * @return int
     */
    public function getUploads()
    {
        return Torrent::withAnyStatus()
            ->where('user_id', '=', $this->id)
            ->count();
    }

    /**
     * @method getLeeching
     *
     * Gets the amount of torrents a user seeds
     *
     * @return int
     */
    public function getLeeching()
    {
        return Peer::where('user_id', '=', $this->id)
            ->where('left', '>', '0')
            ->distinct('info_hash')
            ->count();
    }

    /**
     * @method getWarning
     *
     * Gets count on users active warnings
     *
     * @return int
     */
    public function getWarning()
    {
        return Warning::where('user_id', '=', $this->id)
            ->whereNotNull('torrent')
            ->where('active', '=', '1')
            ->count();
    }

    /**
     * @method getTotalSeedTime
     *
     * Gets the users total seedtime
     *
     * @return int
     */
    public function getTotalSeedTime()
    {
        return History::where('user_id', '=', $this->id)
            ->sum('seedtime');
    }

    /**
     * @method getTotalSeedSize
     *
     * Gets the users total seedsoze
     *
     * @return int
     */
    public function getTotalSeedSize()
    {
        $peers = Peer::where('user_id', '=', $this->id)->where('seeder', '=', 1)->pluck('torrent_id');

        return Torrent::whereIn('id', $peers)->sum('size');
    }

    /**
     * @method getCompletedSeeds
     *
     * Gets the users satisfied torrent count.
     *
     * @return int
     */
    public function getCompletedSeeds()
    {
        return History::where('user_id', '=', $this->id)->where('seedtime', '>=', \config('hitrun.seedtime'))->count();
    }

    /**
     * @method getSpecialSeedingSize
     *
     * Gets the seeding size of torrents with at least 15 days seedtime in the past 30 days.
     *
     * @return int
     */
    public function getSpecialSeedingSize()
    {
        $current = Carbon::now();
        $seeding = History::where('user_id', '=', $this->id)->where('completed_at', '<=', $current->copy()->subDays(30)->toDateTimeString())->where('active', '=', 1)->where('seeder', '=', 1)->where('seedtime', '>=', 1296000)->pluck('info_hash');

        return Torrent::whereIn('info_hash', $seeding)->sum('size');
    }
}
