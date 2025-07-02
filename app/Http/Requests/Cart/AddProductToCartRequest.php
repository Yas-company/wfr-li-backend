<?php

namespace App\Http\Requests\Cart;

use App\Enums\ProductStatus;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class AddProductToCartRequest extends FormRequest
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
            'product_id' => [
                'required',
                Rule::exists('products', 'id')
                    ->where('is_active', true)
                    ->where('status', ProductStatus::PUBLISHED)
            ],
            'quantity' => [
                'required',
                'integer',
                'min:1'
            ]
        ];
    }
}
