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

use App\Models\Invite;
use App\Models\User;
use Database\Seeders\GroupSeeder;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\seed;

beforeEach(function (): void {
    seed(GroupSeeder::class);
    Event::fake(Registered::class);
});

test('user registration is not available when disabled', function (): void {
    $this->withoutMiddleware();
    config(['other.invite-only' => true]);

    $this->get('/register')
        ->assertOk()
        ->assertSeeText('Open registration is disabled');
    Event::assertNotDispatched(Registered::class);
});

test('user registration is available when enabled', function (): void {
    $this->withoutMiddleware();
    config([
        'other.invite-only' => false,
        'captcha.enabled'   => false,
    ]);

    $email = fake()->freeEmail;

    $this->get('/register')
        ->assertOk()
        ->assertDontSeeText('Open registration is disabled');

    $this->post('/register', [
        'username'              => 'testuser',
        'email'                 => $email,
        'password'              => 'password',
        'password_confirmation' => 'password',
    ])->assertRedirectToRoute('home.index');

    assertDatabaseHas('users', [
        'username'          => 'testuser',
        'email'             => $email,
        'email_verified_at' => null,
    ]);
    Event::assertDispatched(Registered::class);

    // Email verification for newly registered user
    $user = User::where('email', $email)->first();
    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(5),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );

    $response = $this->get($verificationUrl);

    $this->assertNotNull($user->fresh()->email_verified_at);
});

test('user can register using invite code', function (): void {
    $this->withoutMiddleware();
    config([
        'other.invite-only' => true,
        'captcha.enabled'   => false,
    ]);

    Invite::factory()->create([
        'code'        => 'testcode',
        'accepted_at' => null,
        'accepted_by' => null,
        'expires_on'  => now()->addDays(7),
    ]);

    $email = fake()->freeEmail;

    $this->get('/register?code=testcode')
        ->assertOk()
        ->assertDontSeeText('Open registration is disabled');

    $this->post('/register?code=testcode', [
        'username'              => 'testuser',
        'email'                 => $email,
        'password'              => 'password',
        'password_confirmation' => 'password',
    ])
        ->assertSessionHasNoErrors()
        ->assertRedirectToRoute('home.index');

    assertDatabaseHas('users', [
        'username'          => 'testuser',
        'email'             => $email,
        'email_verified_at' => null,
    ]);
    Event::assertDispatched(Registered::class);

    // Email verification for newly registered user
    $user = User::where('email', $email)->first();
    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(5),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );

    $response = $this->get($verificationUrl);

    $this->assertNotNull($user->fresh()->email_verified_at);
});

test('user cannot register using invalid invite code', function (): void {
    $this->withoutMiddleware();
    config([
        'other.invite-only' => true,
        'captcha.enabled'   => false,
    ]);

    $email = fake()->freeEmail;

    $this->get('/register?code=testcode')
        ->assertOk()
        ->assertDontSeeText('Open registration is disabled');

    $this->post('/register?code=testcode', [
        'username'              => 'testuser',
        'email'                 => $email,
        'password'              => 'password',
        'password_confirmation' => 'password',
    ])
        ->assertSessionHasErrors('code')
        ->assertRedirectToRoute('home.index');

    assertDatabaseMissing('users', [
        'username' => 'testuser',
        'email'    => $email,
    ]);
    Event::assertNotDispatched(Registered::class);
});

test('user cannot confirm email using invalid hash', function (): void {
    $this->withoutMiddleware();
    config([
        'other.invite-only' => true,
        'captcha.enabled'   => false,
    ]);

    Invite::factory()->create([
        'code'        => 'testcode',
        'accepted_at' => null,
        'accepted_by' => null,
        'expires_on'  => now()->addDays(7),
    ]);

    $email = fake()->freeEmail;

    $this->get('/register?code=testcode')
        ->assertOk()
        ->assertDontSeeText('Open registration is disabled');

    $this->post('/register?code=testcode', [
        'username'              => 'testuser',
        'email'                 => $email,
        'password'              => 'password',
        'password_confirmation' => 'password',
    ])
        ->assertSessionHasNoErrors()
        ->assertRedirectToRoute('home.index');

    assertDatabaseHas('users', [
        'username'          => 'testuser',
        'email'             => $email,
        'email_verified_at' => null,
    ]);
    Event::assertDispatched(Registered::class);

    // Email verification for newly registered user with invalid email
    $user = User::where('email', $email)->first();
    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(5),
        ['id' => $user->id, 'hash' => sha1(fake()->safeEmail)]
    );

    $response = $this->get($verificationUrl);

    $this->assertNull($user->fresh()->email_verified_at);
});

test('user can register using invite code with internal note assigned', function (): void {
    $this->withoutMiddleware();
    config([
        'other.invite-only' => true,
        'captcha.enabled'   => false,
    ]);

    $invite = Invite::factory()->create([
        'code'          => 'testcode',
        'accepted_at'   => null,
        'accepted_by'   => null,
        'expires_on'    => now()->addDays(7),
        'internal_note' => 'This is a test note',
    ]);

    $email = fake()->freeEmail;

    $this->get('/register?code=testcode')
        ->assertOk()
        ->assertDontSeeText('Open registration is disabled');

    $this->post('/register?code=testcode', [
        'username'              => 'testuser',
        'email'                 => $email,
        'password'              => 'password',
        'password_confirmation' => 'password',
    ])
        ->assertSessionHasNoErrors()
        ->assertRedirectToRoute('home.index');

    assertDatabaseHas('users', [
        'username'          => 'testuser',
        'email'             => $email,
        'email_verified_at' => null,
    ]);

    $invite->refresh();

    assertDatabaseHas('user_notes', [
        'message'  => 'This is a test note',
        'staff_id' => $invite->user_id,
        'user_id'  => $invite->accepted_by,
    ]);

    Event::assertDispatched(Registered::class);

    // Email verification for newly registered user
    $user = User::where('email', $email)->first();
    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(5),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );

    $response = $this->get($verificationUrl);

    $this->assertNotNull($user->fresh()->email_verified_at);
});
