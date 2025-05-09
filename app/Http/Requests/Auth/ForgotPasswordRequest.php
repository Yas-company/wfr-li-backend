<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => ['required', 'string', 'exists:users,phone']
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => __('messages.validation.required.phone'),
            'phone.exists' => __('messages.validation.exists.phone')
        ];
    }
} 