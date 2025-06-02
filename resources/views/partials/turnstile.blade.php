<script
    src="https://challenges.cloudflare.com/turnstile/v0/api.js"
    async
    defer
    nonce="{{ HDVinnie\SecureHeaders\SecureHeaders::nonce() }}"
></script>
<div
    class="cf-turnstile"
    data-sitekey="{{ config('captcha.turnstile.site_key') }}"
    data-theme="dark"
    data-action="{{ Route::is('login') ? 'login' : (Route::is('register') ? 'register' : (Route::is('password.email') ? 'reset' : (Route::is('verification.send') ? 'verify' : 'auth'))) }}"
    data-size="normal"
    data-appearance="interactive"
></div>
