<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)
                ->mixedCase()
                ->numbers()
                ->symbols()
            ],
            'password_confirmation' => ['required', 'string']
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required' => __('messages.validation.required.current_password'),
            'password.required' => __('messages.validation.required.password'),
            'password.confirmed' => __('messages.validation.confirmed.password'),
            'password.min' => __('messages.validation.min.password'),
            'password.mixed_case' => __('messages.validation.mixed_case.password'),
            'password.numbers' => __('messages.validation.numbers.password'),
            'password.symbols' => __('messages.validation.symbols.password'),
            'password_confirmation.required' => __('messages.validation.required.password_confirmation')
        ];
    }
} 