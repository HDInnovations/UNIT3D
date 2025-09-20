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
 * App\Models\UserSetting.
 *
 * @property int                             $id
 * @property int                             $user_id
 * @property bool                            $censor
 * @property bool                            $news_block_visible
 * @property int                             $news_block_position
 * @property bool                            $chat_block_visible
 * @property int                             $chat_block_position
 * @property bool                            $featured_block_visible
 * @property int                             $featured_block_position
 * @property bool                            $random_media_block_visible
 * @property int                             $random_media_block_position
 * @property bool                            $poll_block_visible
 * @property int                             $poll_block_position
 * @property bool                            $top_torrents_block_visible
 * @property int                             $top_torrents_block_position
 * @property bool                            $top_users_block_visible
 * @property int                             $top_users_block_position
 * @property bool                            $latest_topics_block_visible
 * @property int                             $latest_topics_block_position
 * @property bool                            $latest_posts_block_visible
 * @property int                             $latest_posts_block_position
 * @property bool                            $latest_comments_block_visible
 * @property int                             $latest_comments_block_position
 * @property bool                            $online_block_visible
 * @property int                             $online_block_position
 * @property string                          $locale
 * @property int                             $style
 * @property int                             $torrent_layout
 * @property bool                            $torrent_filters
 * @property ?string                         $custom_css
 * @property ?string                         $standalone_css
 * @property bool                            $show_poster
 * @property bool                            $unbookmark_torrents_on_completion
 * @property bool                            $show_adult_content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class UserSetting extends Model
{
    /** @use HasFactory<\Database\Factories\UserSettingFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array{
     *     censor: 'bool',
     *     news_block_visible: 'bool',
     *     news_block_position: 'int',
     *     chat_block_visible: 'bool',
     *     chat_block_position: 'int',
     *     featured_block_visible: 'bool',
     *     featured_block_position: 'int',
     *     random_media_block_visible: 'bool',
     *     random_media_block_position: 'int',
     *     poll_block_visible: 'bool',
     *     poll_block_position: 'int',
     *     top_torrents_block_visible: 'bool',
     *     top_torrents_block_position: 'int',
     *     top_users_block_visible: 'bool',
     *     top_users_block_position: 'int',
     *     latest_topics_block_visible: 'bool',
     *     latest_topics_block_position: 'int',
     *     latest_posts_block_visible: 'bool',
     *     latest_posts_block_position: 'int',
     *     latest_comments_block_visible: 'bool',
     *     latest_comments_block_position: 'int',
     *     online_block_visible: 'bool',
     *     online_block_position: 'int',
     *     torrent_filters: 'bool',
     *     show_poster: 'bool',
     *     unbookmark_torrents_on_completion: 'bool',
     * }
     */
    protected function casts(): array
    {
        return [
            'censor'                            => 'bool',
            'news_block_visible'                => 'bool',
            'news_block_position'               => 'int',
            'chat_block_visible'                => 'bool',
            'chat_block_position'               => 'int',
            'featured_block_visible'            => 'bool',
            'featured_block_position'           => 'int',
            'random_media_block_visible'        => 'bool',
            'random_media_block_position'       => 'int',
            'poll_block_visible'                => 'bool',
            'poll_block_position'               => 'int',
            'top_torrents_block_visible'        => 'bool',
            'top_torrents_block_position'       => 'int',
            'top_users_block_visible'           => 'bool',
            'top_users_block_position'          => 'int',
            'latest_topics_block_visible'       => 'bool',
            'latest_topics_block_position'      => 'int',
            'latest_posts_block_visible'        => 'bool',
            'latest_posts_block_position'       => 'int',
            'latest_comments_block_visible'     => 'bool',
            'latest_comments_block_position'    => 'int',
            'online_block_visible'              => 'bool',
            'online_block_position'             => 'int',
            'torrent_filters'                   => 'bool',
            'show_poster'                       => 'bool',
            'unbookmark_torrents_on_completion' => 'bool',
            'show_adult_content'                => 'bool',
        ];
    }

    /**
     * Get the user that owns the settings.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
