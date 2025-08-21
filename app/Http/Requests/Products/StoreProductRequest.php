<?php

namespace App\Http\Requests\Products;

use App\Enums\ProductStatus;
use App\Enums\UnitType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|array',
            'name.ar' => 'required|string|max:255',
            'name.en' => 'required|string|max:255',
            'description' => 'required|array',
            'description.ar' => 'required|string',
            'description.en' => 'required|string',
            'base_price' => 'required|numeric|min:0.01',
            'discount_rate' => 'required|numeric|min:0',
            'quantity' => 'required|numeric|min:1',
            'stock_qty' => 'required|integer|min:0',
            'nearly_out_of_stock_limit' => 'nullable|integer|min:0',
            'unit_type' => ['required', Rule::in(UnitType::values())],
            'status' => ['required', Rule::in(ProductStatus::values())],
            'category_id' => ['required', Rule::exists('categories', 'id')],
            'min_order_quantity' => 'required|numeric|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'price.min' => __('messages.product.price.min'),
            'quantity.min' => __('messages.product.quantity.min'),
            'category_id.exists' => __('messages.product.category_id.exists'),
            'unit_type.in' => __('messages.product.unit_type.in'),
            'status.in' => __('messages.product.status.in'),
            'min_order_quantity.required' => __('messages.product.min_order_quantity.required'),
            'min_order_quantity.numeric' => __('messages.product.min_order_quantity.numeric'),
        ];
    }
}
