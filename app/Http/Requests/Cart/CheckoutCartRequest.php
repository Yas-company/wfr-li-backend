<?php

namespace App\Http\Requests\Cart;

use Illuminate\Validation\Rule;
use App\Enums\Order\PaymentMethod;
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
            'shipping_address' => ['required', 'string'],
            'shipping_latitude' => ['required', 'numeric'],
            'shipping_longitude' => ['required', 'numeric'],
            'payment_method' => ['required', Rule::in(PaymentMethod::cases())],
            'notes' => ['nullable', 'string'],
        ];
    }
}
