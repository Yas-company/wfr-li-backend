<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetRelatedBuyersRequest extends FormRequest
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
            'search' => 'nullable|string|min:3|max:255',
            'sort_by' => [
                'nullable',
                'string',
                Rule::in(['name', 'quantity', 'total_price'])
            ],
            'sort_order' => [
                'nullable',
                'string',
                Rule::in(['asc', 'desc'])
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
            'search.string' => 'Search term must be a string.',
            'search.min' => 'Search term must be at least 3 characters.',
            'search.max' => 'Search term cannot exceed 255 characters.',
            'sort_by.in' => 'Sort by field must be one of: name, quantity, total_price.',
            'sort_order.in' => 'Sort order must be either asc or desc.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'search' => 'search term',
            'sort_by' => 'sort field',
            'sort_order' => 'sort order',
        ];
    }
}
