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

namespace Database\Factories;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\UserNotification;
use Override;

/** @extends Factory<UserNotification> */
class UserNotificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = UserNotification::class;

    /**
     * Define the model's default state.
     */
    #[Override]
    public function definition(): array
    {
        return [
            'user_id'                      => User::factory(),
            'block_notifications'          => $this->faker->boolean(),
            'show_bon_gift'                => $this->faker->boolean(),
            'show_mention_forum_post'      => $this->faker->boolean(),
            'show_mention_article_comment' => $this->faker->boolean(),
            'show_mention_request_comment' => $this->faker->boolean(),
            'show_mention_torrent_comment' => $this->faker->boolean(),
            'show_subscription_topic'      => $this->faker->boolean(),
            'show_subscription_forum'      => $this->faker->boolean(),
            'show_forum_topic'             => $this->faker->boolean(),
            'show_following_upload'        => $this->faker->boolean(),
            'show_request_bounty'          => $this->faker->boolean(),
            'show_request_comment'         => $this->faker->boolean(),
            'show_request_fill'            => $this->faker->boolean(),
            'show_request_fill_approve'    => $this->faker->boolean(),
            'show_request_fill_reject'     => $this->faker->boolean(),
            'show_request_claim'           => $this->faker->boolean(),
            'show_request_unclaim'         => $this->faker->boolean(),
            'show_torrent_comment'         => $this->faker->boolean(),
            'show_torrent_tip'             => $this->faker->boolean(),
            'show_torrent_thank'           => $this->faker->boolean(),
            'show_account_follow'          => $this->faker->boolean(),
            'show_account_unfollow'        => $this->faker->boolean(),
            'json_account_groups'          => Group::factory()->count(3)->create()->pluck('id')->toArray(),
            'json_bon_groups'              => Group::factory()->count(3)->create()->pluck('id')->toArray(),
            'json_mention_groups'          => Group::factory()->count(3)->create()->pluck('id')->toArray(),
            'json_request_groups'          => Group::factory()->count(3)->create()->pluck('id')->toArray(),
            'json_torrent_groups'          => Group::factory()->count(3)->create()->pluck('id')->toArray(),
            'json_forum_groups'            => Group::factory()->count(3)->create()->pluck('id')->toArray(),
            'json_following_groups'        => Group::factory()->count(3)->create()->pluck('id')->toArray(),
            'json_subscription_groups'     => Group::factory()->count(3)->create()->pluck('id')->toArray(),
        ];
    }
}
