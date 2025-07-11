<?php

namespace App\Http\Requests\Order;

use Illuminate\Validation\Rule;
use App\Enums\Order\OrderStatus;
use App\Enums\Order\ShippingMethod;
use Illuminate\Foundation\Http\FormRequest;

class FilterOrderRequest extends FormRequest
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
            'status' => ['nullable', Rule::in(OrderStatus::cases())],
            'shipping_method' => ['nullable', Rule::in(ShippingMethod::cases())],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Invalid order status',
            'shipping_method.in' => 'Invalid shipping method',
            'start_date.date' => 'Invalid start date',
            'end_date.date' => 'Invalid end date',
            'end_date.after' => __('messages.validation.end_date.after'),
        ];
    }
}
