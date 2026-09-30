<?php

declare(strict_types=1);

use App\Http\Middleware\SetLanguage;
use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

beforeEach(function (): void {
    config(['app.locale' => 'cs', 'language.allowed' => ['cs', 'en'], 'language.carbon' => true]);
    app()->setLocale('cs');
    Carbon::setLocale('cs');
});

afterEach(function (): void {
    app()->setLocale(config('app.locale'));
    Carbon::setLocale(config('app.locale'));
});

test('request language selects the effective translation and date locale without accepting malformed values', function (mixed $locale, string $expected): void {
    $request = Request::create('/login', 'GET', ['lang' => $locale]);

    $result = (new SetLanguage())->handle($request, fn () => [app()->getLocale(), Carbon::getLocale()]);

    expect($result)->toBe([$expected, $expected]);
})->with([
    'Czech' => ['cs', 'cs'],
    'English' => ['en', 'en'],
    'unsupported locale' => ['xx', 'cs'],
    'array query parameter' => [['en'], 'cs'],
    'numeric query parameter' => [42, 'cs'],
]);

test('guest session language is honored and a request override does not overwrite that preference', function (): void {
    $session = app('session.store');
    $session->put('locale', 'en');
    $request = Request::create('/login');
    $request->setLaravelSession($session);

    (new SetLanguage())->handle($request, fn () => null);
    expect(app()->getLocale())->toBe('en');

    $override = Request::create('/login', 'GET', ['lang' => 'cs']);
    $override->setLaravelSession($session);
    (new SetLanguage())->handle($override, fn () => null);

    expect(app()->getLocale())->toBe('cs')
        ->and($session->get('locale'))->toBe('en');
});

test('account language takes precedence over guest session language and request overrides do not change it', function (): void {
    $settings = (new UserSetting())->forceFill(['locale' => 'en']);
    $user = (new User())->setRelation('settings', $settings);
    $this->actingAs($user);
    $session = app('session.store');
    $session->put('locale', 'cs');
    $request = Request::create('/');
    $request->setLaravelSession($session);

    (new SetLanguage())->handle($request, fn () => null);
    expect(app()->getLocale())->toBe('en');

    $override = Request::create('/', 'GET', ['lang' => 'cs']);
    $override->setLaravelSession($session);
    (new SetLanguage())->handle($override, fn () => null);

    expect(app()->getLocale())->toBe('cs')
        ->and($settings->locale)->toBe('en');
});

test('malformed guest session language falls back to the configured language', function (): void {
    $session = app('session.store');
    $session->put('locale', ['en']);
    $request = Request::create('/login');
    $request->setLaravelSession($session);

    (new SetLanguage())->handle($request, fn () => null);

    expect(app()->getLocale())->toBe('cs')
        ->and(Carbon::getLocale())->toBe('cs');
});
