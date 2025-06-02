<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class TurnstileCaptcha
{
    /**
     * Render the Turnstile captcha.
     */
    public static function render(): string
    {
        return view('partials.turnstile')->render();
    }

    /**
     * Validate the Turnstile response.
     *
     * @param array<string, string> $formData The form data containing the turnstile response
     *
     * @throws \Exception
     */
    public static function check(array $formData): bool
    {
        $token = $formData['cf-turnstile-response'] ?? null;

        if (!$token) {
            return false;
        }

        if (!\is_string($token)) {
            return false;
        }

        if (\strlen($token) < 100 || \strlen($token) > 3000) {
            return false;
        }

        try {
            $response = Http::asForm()
                ->timeout(5)
                ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                    'secret'   => config('captcha.turnstile.secret_key'),
                    'response' => $token,
                    'remoteip' => request()->ip(),
                ]);

            if (!$response->successful()) {
                return false;
            }

            $result = $response->json();

            return ! (!$result['success'])
            ;
        } catch (Exception $e) {
            Log::error('Turnstile: Exception during verification', [
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }
}
