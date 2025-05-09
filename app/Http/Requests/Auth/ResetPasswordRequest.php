<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'phone' => ['required', 'string', 'exists:users,phone'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'phone.required' => __('messages.validation.required', ['field' => 'رقم الهاتف']),
            'phone.exists' => __('messages.validation.exists', ['field' => 'رقم الهاتف']),
            'password.required' => __('messages.validation.required', ['field' => 'كلمة المرور']),
            'password.min' => __('messages.validation.min.string', ['field' => 'كلمة المرور', 'min' => 8]),
            'password.confirmed' => __('messages.validation.confirmed', ['field' => 'كلمة المرور']),
            'password.regex' => __('messages.validation.password_format'),
        ];
    }
} 