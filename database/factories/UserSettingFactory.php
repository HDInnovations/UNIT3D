<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Helpers\Language;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\UserSetting;
use Override;

/**
 * @extends Factory<UserSetting>
 */
class UserSettingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = UserSetting::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'user_id'                           => User::factory(),
            'style'                             => $this->faker->randomNumber(2),
            'torrent_layout'                    => $this->faker->randomElement([0,1,2,3]),
            'custom_css'                        => 'https://'.$this->faker->domainName(),
            'standalone_css'                    => 'https://'.$this->faker->domainName(),
            'show_poster'                       => $this->faker->boolean(),
            'unbookmark_torrents_on_completion' => $this->faker->boolean(),
            'locale'                            => $this->faker->randomElement(array_keys(Language::allowed())),
            'torrent_sort_field'                => 'bumped_at',
            'torrent_search_autofocus'          => $this->faker->boolean(),
            'auto_freeleech_apply'              => $this->faker->boolean(),
            'auto_freeleech_min_tokens'         => $this->faker->randomNumber(),
            'show_adult_content'                => $this->faker->boolean(),
            'news_block_visible'                => $this->faker->boolean(),
            'news_block_position'               => $this->faker->randomNumber(2),
            'chat_block_visible'                => $this->faker->boolean(),
            'chat_block_position'               => $this->faker->randomNumber(2),
            'featured_block_visible'            => $this->faker->boolean(),
            'featured_block_position'           => $this->faker->randomNumber(2),
            'random_media_block_visible'        => $this->faker->boolean(),
            'random_media_block_position'       => $this->faker->randomNumber(2),
            'poll_block_visible'                => $this->faker->boolean(),
            'poll_block_position'               => $this->faker->randomNumber(2),
            'top_torrents_block_visible'        => $this->faker->boolean(),
            'top_torrents_block_position'       => $this->faker->randomNumber(2),
            'top_users_block_visible'           => $this->faker->boolean(),
            'top_users_block_position'          => $this->faker->randomNumber(2),
            'latest_topics_block_visible'       => $this->faker->boolean(),
            'latest_topics_block_position'      => $this->faker->randomNumber(2),
            'latest_posts_block_visible'        => $this->faker->boolean(),
            'latest_posts_block_position'       => $this->faker->randomNumber(2),
            'latest_comments_block_visible'     => $this->faker->boolean(),
            'latest_comments_block_position'    => $this->faker->randomNumber(2),
            'online_block_visible'              => $this->faker->boolean(),
            'online_block_position'             => $this->faker->randomNumber(2),
        ];
    }
}
