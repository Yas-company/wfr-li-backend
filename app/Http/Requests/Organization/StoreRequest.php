<?php

namespace App\Http\Requests\Organization;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use App\Enums\Organization\OrganizationStatus;

class StoreRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('organizations')
                    ->where('status', OrganizationStatus::APPROVED),
            ],
            'tax_number' => [
                'required',
                'numeric',
                'starts_with:3',
                'digits:16',
                Rule::unique('organizations')
                    ->where('status', OrganizationStatus::APPROVED),
            ],
            'commercial_register_number' => [
                'required',
                'numeric',
                'digits:7',
                Rule::unique('organizations')
                    ->where('status', OrganizationStatus::APPROVED),
            ],
        ];
    }
}
