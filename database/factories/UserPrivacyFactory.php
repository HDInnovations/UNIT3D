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

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\UserPrivacy;
use Override;

/** @extends Factory<UserPrivacy> */
class UserPrivacyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = UserPrivacy::class;

    /**
     * Define the model's default state.
     */
    #[Override]
    public function definition(): array
    {
        return [
            'user_id'                    => User::factory(),
            'hidden'                     => $this->faker->boolean(),
            'private_profile'            => $this->faker->boolean(),
            'show_achievement'           => $this->faker->boolean(),
            'show_bon'                   => $this->faker->boolean(),
            'show_comment'               => $this->faker->boolean(),
            'show_download'              => $this->faker->boolean(),
            'show_follower'              => $this->faker->boolean(),
            'show_online'                => $this->faker->boolean(),
            'show_peer'                  => $this->faker->boolean(),
            'show_post'                  => $this->faker->boolean(),
            'show_profile'               => $this->faker->boolean(),
            'show_profile_about'         => $this->faker->boolean(),
            'show_profile_achievement'   => $this->faker->boolean(),
            'show_profile_badge'         => $this->faker->boolean(),
            'show_profile_follower'      => $this->faker->boolean(),
            'show_profile_title'         => $this->faker->boolean(),
            'show_profile_bon_extra'     => $this->faker->boolean(),
            'show_profile_comment_extra' => $this->faker->boolean(),
            'show_profile_forum_extra'   => $this->faker->boolean(),
            'show_profile_request_extra' => $this->faker->boolean(),
            'show_profile_torrent_count' => $this->faker->boolean(),
            'show_profile_torrent_extra' => $this->faker->boolean(),
            'show_profile_torrent_ratio' => $this->faker->boolean(),
            'show_profile_torrent_seed'  => $this->faker->boolean(),
            'show_profile_warning'       => $this->faker->boolean(),
            'show_rank'                  => $this->faker->boolean(),
            'show_requested'             => $this->faker->boolean(),
            'show_topic'                 => $this->faker->boolean(),
            'show_upload'                => $this->faker->boolean(),
            'show_wishlist'              => $this->faker->boolean(),
            'json_profile_groups'        => $this->faker->word(),
            'json_torrent_groups'        => $this->faker->word(),
            'json_forum_groups'          => $this->faker->word(),
            'json_bon_groups'            => $this->faker->word(),
            'json_comment_groups'        => $this->faker->word(),
            'json_wishlist_groups'       => $this->faker->word(),
            'json_follower_groups'       => $this->faker->word(),
            'json_achievement_groups'    => $this->faker->word(),
            'json_rank_groups'           => $this->faker->word(),
            'json_request_groups'        => $this->faker->word(),
            'json_other_groups'          => $this->faker->word(),
        ];
    }
}
