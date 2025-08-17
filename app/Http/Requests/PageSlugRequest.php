<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PageSlugRequest extends FormRequest
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
            'slug' => 'required|string|exists:pages,slug',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => $this->route('slug'),
        ]);
    }

    public function messages(): array
    {
        return [
            'slug.required' => __('messages.page.slug_required'),
            'slug.exists' => __('messages.page.slug_not_found'),
        ];
    }
}
