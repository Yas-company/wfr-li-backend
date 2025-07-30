<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class BuyerRegistrationRequest extends FormRequest
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
                    $exists = User::query()
                        ->where('phone', $value)
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
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('messages.validation.required.name'),
            'phone.required' => __('messages.validation.required.phone'),
            'phone.unique' => __('messages.validation.unique.phone'),
            'address.required' => __('messages.validation.required.address'),
        ];
    }
}
