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

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\UserPrivacy.
 *
 * @property int       $id
 * @property int       $user_id
 * @property int       $private_profile
 * @property bool      $hidden
 * @property int       $show_achievement
 * @property int       $show_bon
 * @property int       $show_comment
 * @property int       $show_download
 * @property int       $show_follower
 * @property int       $show_online
 * @property int       $show_peer
 * @property int       $show_post
 * @property int       $show_profile
 * @property int       $show_profile_about
 * @property int       $show_profile_achievement
 * @property int       $show_profile_badge
 * @property int       $show_profile_follower
 * @property int       $show_profile_title
 * @property int       $show_profile_bon_extra
 * @property int       $show_profile_comment_extra
 * @property int       $show_profile_forum_extra
 * @property int       $show_profile_request_extra
 * @property int       $show_profile_torrent_count
 * @property int       $show_profile_torrent_extra
 * @property int       $show_profile_torrent_ratio
 * @property int       $show_profile_torrent_seed
 * @property int       $show_profile_warning
 * @property int       $show_rank
 * @property int       $show_requested
 * @property int       $show_topic
 * @property int       $show_upload
 * @property int       $show_wishlist
 * @property list<int> $json_profile_groups
 * @property list<int> $json_torrent_groups
 * @property list<int> $json_forum_groups
 * @property list<int> $json_bon_groups
 * @property list<int> $json_comment_groups
 * @property list<int> $json_wishlist_groups
 * @property list<int> $json_follower_groups
 * @property list<int> $json_achievement_groups
 * @property list<int> $json_rank_groups
 * @property list<int> $json_request_groups
 * @property list<int> $json_other_groups
 */
class UserPrivacy extends Model
{
    /** @use HasFactory<\Database\Factories\UserPrivacyFactory> */
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_privacy';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array{hidden: 'bool', json_profile_groups: 'array', json_torrent_groups: 'array', json_forum_groups: 'array', json_bon_groups: 'array', json_comment_groups: 'array', json_wishlist_groups: 'array', json_follower_groups: 'array', json_achievement_groups: 'array', json_rank_groups: 'array', json_request_groups: 'array', json_other_groups: 'array'}
     */
    protected function casts(): array
    {
        return [
            'hidden'                  => 'bool',
            'json_profile_groups'     => 'array',
            'json_torrent_groups'     => 'array',
            'json_forum_groups'       => 'array',
            'json_bon_groups'         => 'array',
            'json_comment_groups'     => 'array',
            'json_wishlist_groups'    => 'array',
            'json_follower_groups'    => 'array',
            'json_achievement_groups' => 'array',
            'json_rank_groups'        => 'array',
            'json_request_groups'     => 'array',
            'json_other_groups'       => 'array',
        ];
    }

    /**
     * Get the user that owns the privacy settings.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
