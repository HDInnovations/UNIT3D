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

use App\Models\User;

test('user factory returns correct values when created', function (): void {
    $user = User::factory()->create();

    $user->makeVisible([
        'email',
        'password',
        'passkey',
        'rsskey',
        'remember_token',
        'api_token',
    ]);

    expect($user)
        ->toBeInstanceOf(User::class)
        ->toHaveKeys([
            'username',
            'email',
            'email_verified_at',
            'password',
            'passkey',
            'group_id',
            'uploaded',
            'downloaded',
            'image',
            'title',
            'about',
            'signature',
            'fl_tokens',
            'seedbonus',
            'invites',
            'hitandruns',
            'rsskey',
            'chatroom_id',
            'read_rules',
            'can_chat',
            'can_comment',
            'can_download',
            'can_request',
            'can_invite',
            'can_upload',
            'remember_token',
            'api_token',
            'last_login',
            'last_action',
            //    'disabled_at',
            //    'deleted_by',
            'chat_status_id',
        ]);
});
