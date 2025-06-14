<?php

namespace App\Http\Requests\Auth;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class SelectRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'role' => ['required', 'string', 'in:' . implode(',', [UserRole::BUYER->value, UserRole::SUPPLIER->value])],
        ];
    }

    public function messages(): array
    {
        return [
            'role.required' => __('messages.validation.required.role'),
            'role.in' => __('messages.validation.in.role'),
        ];
    }
} 