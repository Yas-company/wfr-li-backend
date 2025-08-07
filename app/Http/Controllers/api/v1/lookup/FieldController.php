<?php

namespace App\Http\Controllers\api\v1\lookup;

use App\Http\Controllers\Controller;
use App\Http\Resources\FieldResource;
use App\Models\Field;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class FieldController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the fields.
     *
     * @return JsonResponse
     * 
     * @OA\Get(
     *     path="/fields",
     *     summary="Get all fields",
     *     description="Get all fields",
     *     tags={"Fields"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Fields retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Fields retrieved successfully"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="object",
     *                         @OA\Property(property="en", type="string", example="Field 1"),
     *                         @OA\Property(property="ar", type="string", example="الحقل 1"),
     *                     ),
     *                     @OA\Property(property="created_at", type="string", example="2021-01-01 00:00:00"),
     *                     @OA\Property(property="updated_at", type="string", example="2021-01-01 00:00:00"),
     *                 ),       
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing token",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Not authorized to access fields",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You are not authorized to access fields"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Internal server error"),
     *         ),
     *     ),
     * )    
     */
    public function index(): JsonResponse
    {
        $fields = Field::all();
        return $this->successResponse(FieldResource::collection($fields));
    }

    /**
     * Display the specified field.
     *
     * @param Field $field
     * @return JsonResponse
     * 
     * @OA\Get(
     *     path="/fields/{id}",
     *     summary="Get a field by ID",
     *     description="Get a specific field by its ID",
     *     tags={"Fields"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Field ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Field retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Field retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="object",
     *                     @OA\Property(property="en", type="string", example="Field 1"),
     *                     @OA\Property(property="ar", type="string", example="الحقل 1"),
     *                 ),
     *                 @OA\Property(property="created_at", type="string", example="2021-01-01 00:00:00"),
     *                 @OA\Property(property="updated_at", type="string", example="2021-01-01 00:00:00"),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid field ID",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid field ID"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object",
     *                     @OA\Property(property="id", type="array",
     *                         @OA\Items(type="string", example="The field ID must be a valid integer."),
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing token",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Not authorized to access fields",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You are not authorized to access fields"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Field not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Field not found"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object",
     *                     @OA\Property(property="id", type="array",
     *                         @OA\Items(type="string", example="The specified field does not exist."),
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Internal server error"),
     *         ),
     *     ),
     * )
     */
    public function show(Field $field): JsonResponse
    {
        return $this->successResponse(new FieldResource($field));
    }

}
