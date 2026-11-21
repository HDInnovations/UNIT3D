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

use App\Models\Bot;
use App\Models\Chatroom;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ChatConversation;
use Override;

/** @extends Factory<ChatConversation> */
class ChatConversationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = ChatConversation::class;

    /**
     * Define the model's default state.
     */
    #[Override]
    public function definition(): array
    {
        return [
            'user_id'   => User::factory(),
            'room_id'   => Chatroom::factory(),
            'target_id' => User::factory(),
            'bot_id'    => Bot::factory(),
            'audible'   => $this->faker->boolean(),
        ];
    }
}
