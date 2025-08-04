<?php

namespace App\Http\Requests\Cart;

use App\Enums\Order\OrderType;
use Illuminate\Validation\Rule;
use App\Enums\Order\PaymentMethod;
use App\Enums\Order\ShippingMethod;
use Illuminate\Foundation\Http\FormRequest;

class CheckoutCartRequest extends FormRequest
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
            'shipping_address_id' => ['required', Rule::exists('addresses', 'id')->where('user_id', auth()->user()->id)],
            'payment_method' => ['required', Rule::in(PaymentMethod::cases())],
            'notes' => ['nullable', 'string'],
            'shipping_method' => ['required', Rule::in(ShippingMethod::cases())],
            'order_type' => ['required', Rule::in(OrderType::cases())],
        ];
    }
}
