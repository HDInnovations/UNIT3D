<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class VerifyIrckeyRequest extends FormRequest
{
    public function authorize(): bool
    {
        $caller = $this->user();

        if ($caller === null) {
            return false;
        }

        if ($caller->id === User::SYSTEM_USER_ID) {
            return true;
        }

        return $caller->group?->slug === 'bot';
    }

    public function rules(): array
    {
        return [
            'irckey'   => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255'],
        ];
    }

    public function prepareForValidation(): void
    {
        if ($this->has('username')) {
            $this->merge([
                'username' => trim((string) $this->input('username')),
            ]);
        }
    }
}
