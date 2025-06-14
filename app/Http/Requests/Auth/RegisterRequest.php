<?php

namespace App\Http\Requests\Auth;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
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

                    // For buyers, also check unverified accounts
                    if ($role === UserRole::BUYER->value) {
                        $unverifiedExists = \App\Models\User::where('phone', $value)
                            ->where('is_verified', false)
                            ->whereNull('deleted_at')
                            ->exists();
                        
                        if ($unverifiedExists) {
                            $fail(__('messages.validation.unique.phone'));
                        }
                    }
                }
            ],
            'country_code' => ['required'],
            'address' => ['required', 'string'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'business_name' => ['required', 'string'],
            'email' => [
                'nullable', 
                'string', 
                'email', 
                'max:255',
                function ($attribute, $value, $fail) {
                    $role = $this->validated('role');
                    if ($value && $role === UserRole::BUYER->value) {
                        $exists = \App\Models\User::where('email', $value)
                            ->where('is_verified', true)
                            ->whereNull('deleted_at')
                            ->exists();
                        
                        if ($exists) {
                            $fail(__('messages.validation.unique.email'));
                        }

                        // Also check unverified accounts for buyers
                        $unverifiedExists = \App\Models\User::where('email', $value)
                            ->where('is_verified', false)
                            ->whereNull('deleted_at')
                            ->exists();
                        
                        if ($unverifiedExists) {
                            $fail(__('messages.validation.unique.email'));
                        }
                    }
                }
            ],
            'password' => ['required', 'confirmed', Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
            ],
            'role' => ['required', 'string', 'in:' . implode(',', UserRole::values())],
        ];

        // Add supplier-specific validation rules
        if ($this->request->get('role') === UserRole::SUPPLIER->value) {
            $rules['license_attachment'] = ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'];
            $rules['commercial_register_attachment'] = ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'];
            $rules['field_id'] = ['required', 'exists:fields,id'];
        }

        return $rules;
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
            'role.required' => __('messages.validation.required.role'),
            'role.in' => __('messages.validation.in.role'),
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