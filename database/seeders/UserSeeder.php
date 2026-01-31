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

namespace Database\Seeders;

use App\Models\Chatroom;
use App\Models\ChatStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ChatStatusSeeder::class,
            ChatroomSeeder::class,
            GroupSeeder::class,
        ]);

        User::query()->upsert([
            [
                'id'                => User::SYSTEM_USER_ID,
                'username'          => 'System',
                'email'             => config('unit3d.default-owner-email'),
                'email_verified_at' => now(),
                'group_id'          => 9,
                'password'          => Hash::make(config('unit3d.default-owner-password')),
                'passkey'           => md5(random_bytes(60)),
                'rsskey'            => md5(random_bytes(60)),
                'api_token'         => Str::random(100),
                'chatroom_id'       => Chatroom::query()
                    ->when(
                        \is_int(config('chat.system_chatroom')),
                        fn ($query) => $query->where('id', '=', config('chat.system_chatroom')),
                        fn ($query) => $query->where('name', '=', config('chat.system_chatroom')),
                    )
                    ->soleValue('id'),
                'chat_status_id' => ChatStatus::query()->value('id'),
            ],
            [
                'id'                => 2,
                'username'          => 'Bot',
                'email'             => config('unit3d.default-owner-email'),
                'email_verified_at' => now(),
                'group_id'          => 9,
                'password'          => Hash::make(config('unit3d.default-owner-password')),
                'passkey'           => md5(random_bytes(60)),
                'rsskey'            => md5(random_bytes(60)),
                'api_token'         => Str::random(100),
                'chatroom_id'       => Chatroom::query()
                    ->when(
                        \is_int(config('chat.system_chatroom')),
                        fn ($query) => $query->where('id', '=', config('chat.system_chatroom')),
                        fn ($query) => $query->where('name', '=', config('chat.system_chatroom')),
                    )
                    ->soleValue('id'),
                'chat_status_id' => ChatStatus::query()->value('id'),
            ],
            [
                'id'                => 3,
                'username'          => config('unit3d.owner-username'),
                'email'             => config('unit3d.default-owner-email'),
                'email_verified_at' => now(),
                'group_id'          => 10,
                'password'          => Hash::make(config('unit3d.default-owner-password')),
                'passkey'           => md5(random_bytes(60)),
                'rsskey'            => md5(random_bytes(60)),
                'api_token'         => Str::random(100),
                'chatroom_id'       => Chatroom::query()
                    ->when(
                        \is_int(config('chat.system_chatroom')),
                        fn ($query) => $query->where('id', '=', config('chat.system_chatroom')),
                        fn ($query) => $query->where('name', '=', config('chat.system_chatroom')),
                    )
                    ->soleValue('id'),
                'chat_status_id' => ChatStatus::query()->value('id'),
            ],
        ], ['username'], ['updated_at' => DB::raw('updated_at')]);
    }
}
