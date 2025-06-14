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

use App\Models\Chatroom;
use App\Models\ChatStatus;
use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Str;

/** @extends Factory<User> */
class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'username'          => $this->faker->unique()->userName(),
            'email'             => 'unit3d@protnmail.com',
            'email_verified_at' => $this->faker->dateTime(),
            'password'          => Hash::make('password'),
            'passkey'           => md5(random_bytes(60)),
            'group_id'          => Group::factory(),
            'uploaded'          => $this->faker->randomNumber(),
            'downloaded'        => $this->faker->randomNumber(),
            'image'             => null,
            'title'             => $this->faker->sentence(),
            'about'             => $this->faker->text(),
            'signature'         => $this->faker->text(),
            'fl_tokens'         => $this->faker->randomNumber(),
            'seedbonus'         => $this->faker->randomFloat(),
            'invites'           => $this->faker->randomNumber(),
            'hitandruns'        => $this->faker->randomNumber(),
            'rsskey'            => md5(random_bytes(60)),
            'chatroom_id'       => Chatroom::factory(),
            'read_rules'        => $this->faker->boolean(),
            'can_chat'          => $this->faker->boolean(),
            'can_comment'       => $this->faker->boolean(),
            'can_download'      => $this->faker->boolean(),
            'can_request'       => $this->faker->boolean(),
            'can_invite'        => $this->faker->boolean(),
            'can_upload'        => $this->faker->boolean(),
            'remember_token'    => Str::random(10),
            'api_token'         => $this->faker->uuid(),
            'last_login'        => $this->faker->dateTime(),
            'last_action'       => $this->faker->dateTime(),
            //'disabled_at'         => $this->faker->dateTime(),
            //'deleted_by'          => \App\Models\User::factory(),
            'chat_status_id' => ChatStatus::factory(),
            'own_flushes'    => $this->faker->boolean(),
        ];
    }

    public function system(): self
    {
        return $this->state(fn (array $attributes) => [
            'id'       => User::SYSTEM_USER_ID,
            'email'    => config('unit3d.default-owner-email'),
            'group_id' => 9,
        ]);
    }
}
