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

use App\Models\Topic;
use App\Models\User;

test('update returns an ok response', function (): void {
    $topic = Topic::factory()->create();
    $user = User::factory()->moderator()->create();
    $data = [
        'approved'    => random_int(0, 1),
        'denied'      => random_int(0, 1),
        'solved'      => random_int(0, 1),
        'invalid'     => random_int(0, 1),
        'bug'         => random_int(0, 1),
        'suggestion'  => random_int(0, 1),
        'implemented' => random_int(0, 1),
    ];

    unset($data[array_rand($data)]);

    $this->actingAs($user)->patch(route('topics.labels', ['topic' => $topic]), $data)
        ->assertRedirectToRoute('topics.show', ['id' => $topic->id])
        ->assertSessionHas('info', 'Label change has been applied');

    $this->assertDatabaseHas('topics', [
        'id' => $topic->id,
        ...$data,
    ]);
});
