<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestOtpAuthRequest extends FormRequest
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
            'phone' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $exists = \App\Models\User::where('phone', $value)
                        ->where('is_verified', true)
                        ->whereNull('deleted_at')
                        ->exists();

                    if ($exists) {
                        $fail(__('messages.validation.unique.phone'));
                    }
                },
            ],
        ];
    }
}
