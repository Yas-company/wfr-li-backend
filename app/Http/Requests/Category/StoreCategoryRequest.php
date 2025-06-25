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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'categories' => 'required|array|min:1|max:10',
            'categories.*.name' => 'required|array',
            'categories.*.name.ar' => 'required|string|max:255',
            'categories.*.name.en' => 'required|string|max:255',
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
            'categories.required' => 'Categories data is required.',
            'categories.min' => 'At least one category is required.',
            'categories.max' => 'Maximum 10 categories can be created at once.',
            'categories.*.name.required' => 'Category name is required.',
            'categories.*.name.ar.required' => 'Arabic name is required.',
            'categories.*.name.en.required' => 'English name is required.',
            'categories.*.name.ar.max' => 'Arabic name cannot exceed 255 characters.',
            'categories.*.name.en.max' => 'English name cannot exceed 255 characters.',
        ];
    }
} 