<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class IndexCategoryRequest extends FormRequest
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
            'search' => [
                'sometimes',
                'string',
                'min:3',
                'max:255',
                'regex:/^[\p{L}\p{N}\s\-_]+$/u', // Allow letters, numbers, spaces, hyphens, underscores
            ],
            'field_id' => [
                'sometimes',
                'integer',
                'min:1',
                'exists:fields,id',
            ],

        ];
    }

    public function messages(): array
    {
        return [
            'search.min' => __('messages.errors.search_term_min'),
            'search.max' => __('messages.errors.search_term_max'),
            'search.regex' => __('messages.errors.search_term_regex'),
            'field_id.exists' => __('messages.errors.field_id_exists'),
            'field_id.integer' => __('messages.errors.field_id_integer'),
        ];
    }

    /**
     * Get validated search term
     */
    public function getSearchTerm(): ?string
    {
        $search = $this->validated('search');

        return $search ? trim($search) : null;
    }

    /**
     * Get validated field ID
     */
    public function getFieldId(): ?int
    {
        return $this->validated('field_id');
    }
}
