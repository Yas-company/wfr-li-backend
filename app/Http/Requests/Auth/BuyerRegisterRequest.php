<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class BuyerRegisterRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'phone' => [
                'required', 
                'string',
                function ($attribute, $value, $fail) {
                    $exists = \App\Models\User::where('phone', $value)
                        ->where('is_verified', true)
                        ->exists();
                    
                    if ($exists) {
                        $fail(__('messages.validation.unique.phone'));
                    }
                }
            ],
            'country_code' => ['required'],
            'address' => ['required', 'string'],
            'location' => ['required', 'string'],
            'business_name' => ['required', 'string'],
            'lic_id' => ['required', 'string', 'unique:users'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
            ],
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
            'name.required' => __('messages.validation.required.name'),
            'phone.required' => __('messages.validation.required.phone'),
            'phone.unique' => __('messages.validation.unique.phone'),
            'country_code.required' => __('messages.validation.required.country_code'),
            'address.required' => __('messages.validation.required.address'),
            'location.required' => __('messages.validation.required.location'),
            'business_name.required' => __('messages.validation.required.business_name'),
            'lic_id.required' => __('messages.validation.required.lic_id'),
            'lic_id.unique' => __('messages.validation.unique.lic_id'),
            'email.email' => __('messages.validation.email'),
            'email.unique' => __('messages.validation.unique.email'),
            'password.required' => __('messages.validation.required.password'),
            'password.confirmed' => __('messages.validation.password.confirmed'),
            'password.min' => __('messages.validation.password.min'),
            'password.mixed_case' => __('messages.validation.password.mixed_case'),
            'password.numbers' => __('messages.validation.password.numbers'),
            'password.symbols' => __('messages.validation.password.symbols'),
        ];
    }
} 