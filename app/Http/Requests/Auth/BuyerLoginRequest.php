<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class BuyerLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => ['required', 'string', 'exists:users,phone'],
            'otp' => ['required', 'string', 'size:6']
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => __('messages.validation.required.phone'),
            'phone.exists' => __('messages.validation.exists.phone'),
            'otp.required' => __('messages.validation.required.otp'),
            'otp.size' => __('messages.validation.size.otp')
        ];
    }
}
