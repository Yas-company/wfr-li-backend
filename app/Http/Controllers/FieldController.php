<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Traits\ApiResponse;
use App\Http\Resources\FieldResource;
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