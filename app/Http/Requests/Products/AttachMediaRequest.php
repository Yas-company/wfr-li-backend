<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class AttachMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'images' => 'required|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'images.required' => __('messages.product.image.required'),
            'images.image' => __('messages.product.image.image'),
            'images.mimes' => __('messages.product.image.mimes'),
            'images.max' => __('messages.product.image.max'),
            'images.*.max' => __('messages.product.image.max_per_file'),
        ];
    }
}
