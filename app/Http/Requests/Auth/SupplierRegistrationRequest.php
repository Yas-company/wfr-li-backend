<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class SupplierRegistrationRequest extends FormRequest
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
                    $role = $this->validated('role');
                    $exists = \App\Models\User::where('phone', $value)
                        ->where('is_verified', true)
                        ->whereNull('deleted_at')
                        ->exists();

                    if ($exists) {
                        $fail(__('messages.validation.unique.phone'));
                    }
                },
            ],
            'address' => ['required', 'array'],
            'address.name' => ['required', 'string'],
            'address.street' => ['required', 'string'],
            'address.city' => ['required', 'string'],
            'address.latitude' => ['required', 'numeric'],
            'address.longitude' => ['required', 'numeric'],
            'address.phone' => [
                'required',
                'string',
            ],
            'business_name' => ['required', 'string'],
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
            ],
            'password' => ['required', 'confirmed', Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols(),
            ],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'license_attachment' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'commercial_register_attachment' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'fields' => ['required', 'array'],
            'fields.*' => ['required', 'exists:fields,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('messages.validation.required.name'),
            'phone.required' => __('messages.validation.required.phone'),
            'phone.unique' => __('messages.validation.unique.phone'),
            'country_code.required' => __('messages.validation.required.country_code'),
            'address.required' => __('messages.validation.required.address'),
            'latitude.numeric' => __('messages.validation.numeric.latitude'),
            'latitude.between' => __('messages.validation.between.latitude'),
            'longitude.numeric' => __('messages.validation.numeric.longitude'),
            'longitude.between' => __('messages.validation.between.longitude'),
            'business_name.required' => __('messages.validation.required.business_name'),
            'email.email' => __('messages.validation.email'),
            'email.unique' => __('messages.validation.unique.email'),
            'password.required' => __('messages.validation.required.password'),
            'password.confirmed' => __('messages.validation.password.confirmed'),
            'password.min' => __('messages.validation.password.min'),
            'password.mixed_case' => __('messages.validation.password.mixed_case'),
            'password.numbers' => __('messages.validation.password.numbers'),
            'password.symbols' => __('messages.validation.password.symbols'),
            'license_attachment.required' => __('messages.validation.required.license_attachment'),
            'license_attachment.file' => __('messages.validation.file.license_attachment'),
            'license_attachment.mimes' => __('messages.validation.mimes.license_attachment'),
            'license_attachment.max' => __('messages.validation.max.license_attachment'),
            'commercial_register_attachment.required' => __('messages.validation.required.commercial_register_attachment'),
            'commercial_register_attachment.file' => __('messages.validation.file.commercial_register_attachment'),
            'commercial_register_attachment.mimes' => __('messages.validation.mimes.commercial_register_attachment'),
            'commercial_register_attachment.max' => __('messages.validation.max.commercial_register_attachment'),
            'field_id.required' => __('messages.validation.required.field_id'),
            'field_id.exists' => __('messages.validation.exists.field_id'),
        ];
    }
}
