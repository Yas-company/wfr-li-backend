<?php

namespace App\Http\Requests\Rating;

use Illuminate\Validation\Rule;
use App\Enums\Morphs\RatingModel;
use Illuminate\Foundation\Http\FormRequest;

class StoreRatingRequest extends FormRequest
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
            'rateable_id' => ['required', 'integer'],
            'rateable_type' => ['required', 'string', Rule::in(RatingModel::getMorphClasses())],
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string'],
        ];
    }
}
