<?php

namespace App\Http\Requests;

use App\Enums\Order\OrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrdersTimeRequest extends FormRequest
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
            'time_filter' => [
                'nullable',
                'string',
                Rule::in(['weekly', 'monthly', 'yearly']),
            ],
            'status' => [
                'nullable',
                'string',
                Rule::in(array_map(fn($case) => $case->value, OrderStatus::cases())),
            ],
        ];
    }
}
