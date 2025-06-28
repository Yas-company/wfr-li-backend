<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization will be handled in the controller
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * 
     */
    public function rules(): array
    {
        return [
            'name' => 'required|array',
            'name.ar' => 'required|string|max:255',
            'name.en' => 'required|string|max:255',
            'field_id' => 'required|exists:fields,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Category name is required.',
            'name.ar.required' => 'Arabic name is required.',
            'name.en.required' => 'English name is required.',
            'name.ar.max' => 'Arabic name cannot exceed 255 characters.',
            'name.en.max' => 'English name cannot exceed 255 characters.',
        ];
    }
} 