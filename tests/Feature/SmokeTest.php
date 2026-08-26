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
 * @author     Roardom <roardom@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Route;

beforeEach(function (): void {
    $this->staffUser = User::factory()->create([
        'group_id' => fn () => Group::factory()->create([
            'is_owner'        => true,
            'is_admin'        => true,
            'is_modo'         => true,
            'is_torrent_modo' => true,
            'can_chat'        => true,
            'can_comment'     => true,
            'can_request'     => true,
            'can_invite'      => true,
            'can_upload'      => true,
        ])->id,
        'can_chat'                => true,
        'can_comment'             => true,
        'can_download'            => true,
        'can_request'             => true,
        'can_invite'              => true,
        'can_upload'              => true,
        'two_factor_confirmed_at' => now()->subHours(config('other.hours-until-invite-after-2fa')),
    ]);
});

test('smoke', function (string $route): void {
    // Hardcode the testing user's username, because we know they exist
    $route = preg_replace('/^users\/\{user\}/', "users/{$this->staffUser->username}", $route);

    $response = $this
        ->actingAs($this->staffUser)
        ->withSession([
            'auth.password_confirmed_at'   => now()->unix(),
            'auth.two_factor_confirmed_at' => now()->unix(),
        ])
        ->get($route);

    $response->assertStatus(200);
})
    ->with(
        collect(Route::getRoutes())
            ->filter(
                fn ($route) => \in_array('GET', $route->methods())
                    // Exclude API because it requires bearer token
                    && !str_starts_with($route->uri(), 'api/')
            )
            ->map(fn ($route) => $route->uri())
            // Exclude routes that have parameters, except for `/users/{user}` which we hardcode in the test
            ->filter(
                fn ($route) => (
                    !str_contains($route, '{')
                    || (substr_count($route, '{') === 1 && preg_match('/^users\/\{user\}/', $route))
                )
                && !\in_array($route, [
                    '_debugbar/open',
                    '_debugbar/assets',
                    '_ignition/health-check',
                    'broadcasting/auth',
                    'application',
                    'forgot-password',
                    'login',
                    'two-factor-challenge',
                    'register',
                ])
            )
            ->values()
            ->all()
    );
