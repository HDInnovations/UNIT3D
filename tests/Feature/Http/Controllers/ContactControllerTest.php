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

use App\Mail\Contact;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\Mail;

test('index returns an ok response', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('contact.index'));
    $response->assertOk();
    $response->assertViewIs('contact.index');
});

test('store returns an ok response', function (): void {
    $this->seed(UserSeeder::class);

    Mail::fake();

    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('contact.store'), [
        'contact-name' => $user->username,
        'email'        => $user->email,
        'message'      => 'This is a test message.',
    ]);

    Mail::assertSent(Contact::class);

    $response->assertRedirect(route('home.index'))->assertSessionHas('success', 'Your message was successfully sent');
});
