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

use App\Models\Post;
use App\Models\PostTip;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Override;

/** @extends Factory<PostTip> */
class PostTipFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = PostTip::class;

    /**
     * Define the model's default state.
     */
    #[Override]
    public function definition(): array
    {
        return [
            'post_id'      => Post::factory(),
            'bon'          => $this->faker->randomNumber(3, true),
            'sender_id'    => User::factory(),
            'recipient_id' => User::factory(),
        ];
    }
}
