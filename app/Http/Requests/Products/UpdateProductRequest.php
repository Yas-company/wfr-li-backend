<?php

namespace App\Http\Requests\Products;

use App\Enums\ProductStatus;
use App\Enums\UnitType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('product'));
    }

    public function rules(): array
    {
        return [
            'name' => 'required|array',
            'name.ar' => 'required|string|max:255',
            'name.en' => 'required|string|max:255',
            'description' => 'required|array',
            'description.ar' => 'required|string|max:255',
            'description.en' => 'required|string|max:255',
            'price' => 'required|numeric|min:0.01',
            'quantity' => 'required|numeric|min:0',
            'stock_qty' => 'required|integer|min:0',
            'nearly_out_of_stock_limit' => 'nullable|integer|min:0',
            'unit_type' => ['required', Rule::in(UnitType::values())],
            'status' => ['required', Rule::in(ProductStatus::values())],
            'category_id' => ['required', Rule::exists('categories', 'id')->where('supplier_id', auth()->user()->id)],
            'image' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'min_order_quantity' => 'required|numeric|min:1',

        ];
    }

    public function messages(): array
    {
        return [
            'price.min' => __('messages.price.min'),
            'category_id.exists' => __('messages.category_id.exists'),
            'unit_type.in' => __('messages.unit_type.in'),
            'status.in' => __('messages.status.in'),
        ];
    }
}
