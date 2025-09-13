<?php

namespace App\Http\Requests;

use App\Enums\Settings\OrderSettings;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class SetSupplierSettingRequest extends FormRequest
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
            'key' => ['required', new Enum(OrderSettings::class)],
            'value' => $this->getValueRules(),
        ];
    }

    protected function getValueRules(): array
    {
        $rules = [
            OrderSettings::ORDER_MIN_ORDER_AMOUNT->value => ['required', 'numeric', 'min:0'],
        ];
        
        return $rules[$this->input('key')] ?? ['required'];
    }
}
