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

namespace App\Providers;

use App\Models\BlockedIp;
use App\Models\FailedLoginAttempt;
use App\Models\Group;
use App\Models\User;
use App\Notifications\FailedLogin;
use App\Services\Unit3dAnnounce;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Fortify;
use Override;

use function Illuminate\Support\defer;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    #[Override]
    public function register(): void
    {
        Fortify::ignoreRoutes();

        // Handle redirects after successful login
        $this->app->instance(LoginResponse::class, new class () implements LoginResponse {
            #[Override]
            public function toResponse($request): \Illuminate\Http\RedirectResponse
            {
                $user = $request->user()->load('group:id,slug');

                // Check if user is disabled
                if ($user->group->slug === 'disabled') {
                    $user->group_id = Group::query()->where('slug', '=', 'user')->soleValue('id');
                    $user->can_download = true;
                    $user->disabled_at = null;
                    $user->save();

                    cache()->forget('user:'.$user->passkey);

                    Unit3dAnnounce::addUser($user);

                    return to_route('home.index')
                        ->with('success', trans('auth.welcome-restore'));
                }

                // Check if user has read the rules
                if ($user->read_rules == 0 && $user->hasVerifiedEmail()) {
                    return redirect()->to(config('other.rules_url'))
                        ->with('warning', trans('auth.require-rules'));
                }

                // Fix for issue described in https://github.com/laravel/framework/pull/46133
                if ($rootUrlOverride = config('unit3d.root_url_override')) {
                    $url = redirect()->getIntendedUrl();

                    return $url === null ? $rootUrlOverride : redirect(
                        rtrim(
                            rtrim($rootUrlOverride, '/')
                            .parse_url($url, PHP_URL_PATH)
                            .'?'.parse_url($url, PHP_URL_QUERY),
                        )
                    );
                }

                return redirect()->intended()
                    ->with('success', trans('auth.welcome'));
            }
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::loginView(fn () => view('auth.login'));
        Fortify::twoFactorChallengeView(fn () => view('auth.two-factor-challenge'));

        Fortify::authenticateUsing(function (Request $request): \Illuminate\Database\Eloquent\Model {
            $request->validate([
                'username' => 'required|string',
                'password' => 'required|string',
                'captcha'  => Rule::when(config('captcha.enabled'), 'hiddencaptcha')
            ]);

            $user = User::query()->where('username', $request->username)->first();

            if ($user === null) {
                throw ValidationException::withMessages([
                    Fortify::username() => __('auth.failed'),
                ]);
            }

            $password = Hash::check($request->password, $user->password);

            if ($password === false) {
                defer(function () use ($user, $request): void {
                    $ip = $request->ip();

                    FailedLoginAttempt::query()->create([
                        'user_id'    => $user->id,
                        'username'   => $request->username,
                        'ip_address' => $ip,
                    ]);

                    $otherUsernamesAttempted = FailedLoginAttempt::query()
                        ->select('username')
                        ->distinct()
                        ->where('ip_address', '=', $ip)
                        ->where('created_at', '>', now()->subSeconds(config('other.auth.multi-account.interval')))
                        ->pluck('username');

                    if ($otherUsernamesAttempted->count() >= config('other.auth.multi-account.max-usernames')) {
                        BlockedIp::query()->upsert([[
                            'ip_address' => $ip,
                            'user_id'    => User::SYSTEM_USER_ID,
                            'reason'     => 'Multi-account abuse: Attempted '.$otherUsernamesAttempted->count().' separate usernames: '.$otherUsernamesAttempted->join(', '),
                            'expires_at' => now()->addSeconds(config('other.auth.multi-account.blocked-for')),
                        ]], ['ip_address']);

                        cache()->forget('blocked-ips');
                    }

                    $ipAttemptCount = FailedLoginAttempt::query()
                        ->where('ip_address', '=', $ip)
                        ->where('created_at', '>', now()->subSeconds(config('other.auth.brute-force.interval')))
                        ->count();

                    if ($ipAttemptCount >= config('other.auth.brute-force.max-attempts')) {
                        BlockedIp::query()->upsert([[
                            'ip_address' => $ip,
                            'user_id'    => User::SYSTEM_USER_ID,
                            'reason'     => 'Brute-force attempt: 6 failed attempts in last '.config('other.auth.brute-force.interval').' s',
                            'expires_at' => now()->addSeconds(config('other.auth.brute-force.blocked-for')),
                        ]], ['ip_address'], ['expires_at' => DB::raw('expires_at')]);

                        cache()->forget('blocked-ips');
                    }

                    $user->notify(new FailedLogin($ip ?? 'Invalid IP'));
                })->always();

                throw ValidationException::withMessages([
                    Fortify::username() => __('auth.failed'),
                ]);
            }

            if ($password === true) {
                $user->load('group:id,slug');

                // Check if user is banned
                if ($user->group->slug === 'banned') {
                    $request->session()->invalidate();

                    throw ValidationException::withMessages([
                        Fortify::username() => __('auth.banned'),
                    ]);
                }

                return $user;
            }
        });
    }
}
