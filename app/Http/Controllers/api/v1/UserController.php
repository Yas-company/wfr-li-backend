<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchSupplierRequest;
use App\Http\Resources\FieldResource;
use App\Http\Resources\SupplierDetailsResource;
use App\Http\Resources\SupplierResource;
use App\Http\Services\UserService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="User",
 *     description="User endpoints"
 * )
 */
class UserController extends Controller
{
    use ApiResponse;
    protected $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Get paginated list of suppliers
     *
     * @OA\Get(
     *     path="/suppliers",
     *     summary="Get suppliers",
     *     description="Get a paginated list of approved suppliers, optionally filtered by search term and distance from buyer location",
     *     tags={"Users"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search term for supplier name or business name",
     *         required=false,
     *         @OA\Schema(type="string", example="ABC Company")
     *     ),
     *     @OA\Parameter(
     *         name="distance",
     *         in="query",
     *         description="Search radius distance in kilometers",
     *         required=false,
     *         @OA\Schema(type="number", format="float", example=10.5)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Suppliers retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Suppliers retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/SupplierResource")),
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(property="last_page", type="integer", example=5),
     *             @OA\Property(property="per_page", type="integer", example=15),
     *             @OA\Property(property="total", type="integer", example=75),
     *             @OA\Property(property="from", type="integer", example=1),
     *             @OA\Property(property="to", type="integer", example=15),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request - Invalid distance value or buyer location not available",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid distance value"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing token",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User not authenticated"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Not authorized to access suppliers",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You are not authorized to access suppliers"),
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
    public function suppliers(Request $request)
    {
        $result = $this->userService->suppliers($request);
        if (isset($result['error'])) {
            return $this->errorResponse($result['error'], 400);
        }

        return $this->paginatedResponse($result, SupplierResource::collection($result),'Suppliers retrieved successfully',statusCode: 200);
    }

    /**
     * Get supplier details by ID
     *
     * @OA\Get(
     *     path="/suppliers/{user}",
     *     summary="Get supplier details",
     *     description="Get detailed information about a specific supplier",
     *     tags={"Users"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         description="User/Supplier ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Supplier retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Supplier retrieved successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/SupplierDetailsResource")
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
     *         description="Forbidden - Not authorized to access supplier data",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You are not authorized to access supplier data"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Supplier not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Supplier not found"),
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
    public function show(int $user_id)
    {
        $result = $this->userService->show($user_id);
        if (isset($result['error'])) {
            return $this->errorResponse($result['error'], 404);
        }

        return $this->successResponse(new SupplierDetailsResource($result),'Supplier retrieved successfully',200);
    }

    /**
     * Search suppliers by name or other criteria
     *
     * @OA\Post(
     *     path="/suppliers/search",
     *     summary="Search suppliers",
     *     description="Search for suppliers using search criteria",
     *     tags={"Users"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"search"},
     *             @OA\Property(property="search", type="string", example="ABC Company", description="Search term for supplier name or business name"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Suppliers retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Suppliers retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/SupplierResource")),
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(property="last_page", type="integer", example=3),
     *             @OA\Property(property="per_page", type="integer", example=15),
     *             @OA\Property(property="total", type="integer", example=42),
     *             @OA\Property(property="from", type="integer", example=1),
     *             @OA\Property(property="to", type="integer", example=15),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object",
     *                     @OA\Property(property="search", type="array",
     *                         @OA\Items(type="string", example="The search field is required."),
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
     *         description="Forbidden - Not authorized to search suppliers",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You are not authorized to search suppliers"),
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
    public function searchSuppliers(SearchSupplierRequest $request)
    {
        $result = $this->userService->searchSuppliers($request);
        if (isset($result['error'])) {
            return $this->errorResponse($result['error'], 500);
        }
        return $this->paginatedResponse($result, SupplierResource::collection($result),'Suppliers retrieved successfully',statusCode: 200);
    }

    /**
     * Get fields for the authenticated supplier
     *
     * @OA\Get(
     *     path="/fields/supplier",
     *     summary="Get supplier fields",
     *     description="Get all fields/industries associated with the authenticated supplier",
     *     tags={"Users"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Fields retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Fields retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/FieldResource"))
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
     *         description="Forbidden - Not authorized as supplier",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You are not authorized to access supplier fields"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No fields found for supplier",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No fields found for supplier"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
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
    public function getSupplierFields()
    {
        $result = $this->userService->getSupplierFields(Auth::user());
        if (isset($result['error'])) {
            return $this->errorResponse($result['error'], 500);
        }
        return $this->successResponse(FieldResource::collection($result),'Fields retrieved successfully',200);

    }

    /**
     * Filter suppliers by various criteria
     *
     * @OA\Get(
     *     path="/suppliers/filter",
     *     summary="Filter suppliers",
     *     description="Filter suppliers by distance from the buyer's location",
     *     tags={"Users"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="distance",
     *         in="query",
     *         description="Maximum distance in kilometers from buyer's location",
     *         required=true,
     *         @OA\Schema(type="number", format="float", example=10.5)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Suppliers retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Suppliers retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/SupplierResource")),
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(property="last_page", type="integer", example=2),
     *             @OA\Property(property="per_page", type="integer", example=15),
     *             @OA\Property(property="total", type="integer", example=28),
     *             @OA\Property(property="from", type="integer", example=1),
     *             @OA\Property(property="to", type="integer", example=15),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request - Distance is required or buyer location not available",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Distance is required"),
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
     *         description="Forbidden - Not authorized to filter suppliers",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You are not authorized to filter suppliers"),
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
    public function filter(Request $request)
    {
        $result = $this->userService->filter($request);
        if (isset($result['error'])) {
            return $this->errorResponse($result['error'], 400);
        }
        return $this->paginatedResponse($result, SupplierResource::collection($result),'Suppliers retrieved successfully',statusCode: 200);
    }
}
