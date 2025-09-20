<?php

namespace App\Http\Requests\Organization;

use App\Enums\Organization\OrganizationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
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
                'nullable',
                'string',
                'max:255',
                Rule::unique('organizations')
                    ->where('status', OrganizationStatus::APPROVED)
                    ->ignore($this->organization->id),
            ],
            'tax_number' => [
                'nullable',
                'numeric',
                'starts_with:3',
                'digits:16',
                Rule::unique('organizations')
                    ->where('status', OrganizationStatus::APPROVED)
                    ->ignore($this->organization->id),
            ],
            'commercial_register_number' => [
                'nullable',
                'numeric',
                'digits:7',
                Rule::unique('organizations')
                    ->where('status', OrganizationStatus::APPROVED)
                    ->ignore($this->organization->id),
            ],
        ];
    }
}
