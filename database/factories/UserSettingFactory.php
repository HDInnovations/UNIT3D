<?php

declare(strict_types=1);

namespace Database\Factories;

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
            'censor'                            => $this->faker->boolean(),
            'style'                             => $this->faker->boolean(),
            'torrent_layout'                    => $this->faker->boolean(),
            'torrent_filters'                   => $this->faker->boolean(),
            'custom_css'                        => $this->faker->word(),
            'standalone_css'                    => $this->faker->word(),
            'show_poster'                       => $this->faker->boolean(),
            'unbookmark_torrents_on_completion' => $this->faker->boolean(),
            'torrent_sort_field'                => 'bumped_at',
        ];
    }
}
