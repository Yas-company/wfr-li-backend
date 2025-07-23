<?php


namespace App\Http\Requests\Products;

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
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|numeric|min:0',
            'stock_qty' => 'required|integer|min:0',
            'nearly_out_of_stock_limit' => 'nullable|integer|min:0',
            'unit_type' => 'required|in:0,1,2,3,4,5,6,7',
            'status' => 'required|in:0,1,2',
            'category_id' => ['required', Rule::exists('categories', 'id')->where('supplier_id', auth()->user()->id)],
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'min_order_quantity' => 'required|numeric|min:1',
        ];
    }
}
