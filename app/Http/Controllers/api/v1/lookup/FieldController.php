<?php

namespace App\Http\Controllers\api\v1\lookup;

use App\Http\Controllers\Controller;
use App\Http\Resources\FieldResource;
use App\Models\Field;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class FieldController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $fields = Field::all();
        return $this->successResponse(FieldResource::collection($fields));
    }

    public function show(Field $field): JsonResponse
    {
        return $this->successResponse(new FieldResource($field));
    }
}
