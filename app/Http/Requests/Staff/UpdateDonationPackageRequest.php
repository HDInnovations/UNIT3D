<?php

declare(strict_types=1);

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDonationPackageRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array<\Illuminate\Contracts\Validation\Rule|string>|string>
     */
    public function rules(): array
    {
        return [
            'position' => [
                'required',
                'min:0',
                'max:9999999',
            ],
            'name' => [
                'required',
                'string',
            ],
            'description' => [
                'required',
                'string',
            ],
            'cost' => [
                'required',
                'numeric',
            ],
            'bonus_value' => [
                'nullable',
                'min:0',
                'max:999999999',
            ],
            'upload_value' => [
                'nullable',
                'min:0',
                'max:9999',
            ],
            'invite_value' => [
                'nullable',
                'min:0',
                'max:9999',
            ],
            'donor_value' => [
                'nullable',
                'min:0',
                'max:9999',
            ],
            'is_active' => [
                'required',
                'boolean',
            ],
        ];
    }
}
