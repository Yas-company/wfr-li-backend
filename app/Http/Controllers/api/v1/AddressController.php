<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Address\StoreAddressRequest;
use App\Http\Requests\Address\UpdateAddressRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Services\AddressService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Annotations as OA;
use App\Exceptions\UserException;

class AddressController extends Controller
{
    use ApiResponse;

    /**
     * AddressController constructor.
     *
     * @param AddressService $addressService
     */
    public function __construct(protected AddressService $addressService)
    {
        //
    }

    /**
     * Display a listing of the user's addresses.
     *
     * @return JsonResponse
     *
     * @OA\Tag(name="Address", description="Address API")
     * @OA\Get(
     *     path="/addresses",
     *     summary="Get all addresses",
     *     description="Get all addresses for the authenticated user",
     *     security={{"bearerAuth":{}}},
     *     tags={"Address"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/AddressResource")
     *             ),
     *             @OA\Property(
     *                 property="links",
     *                 type="object",
     *                 @OA\Property(property="first", type="string", example="http://api.test/addresses?page=1"),
     *                 @OA\Property(property="last", type="string", example="http://api.test/addresses?page=10"),
     *                 @OA\Property(property="next", type="string", nullable=true, example="http://api.test/addresses?page=2"),
     *                 @OA\Property(property="prev", type="string", nullable=true, example=null)
     *             )
     *         )
     *     )
     *     ,
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized"),
     *             @OA\Property(property="errors", nullable=true)
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $addresses = $this->addressService->getAddresses(Auth::user());

        return $this->paginatedResponse(
            $addresses,
            AddressResource::collection($addresses),
            __('messages.success'),
            Response::HTTP_OK
        );

    }

    /**
     * Store a newly created address.
     *
     * @param StoreAddressRequest $request
     *
     * @return JsonResponse
     *
     * @OA\Post(
     *     path="/addresses",
     *     summary="Create address",
     *     description="Create a new address for the authenticated user",
     *     security={{"bearerAuth":{}}},
     *     tags={"Address"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name","street","city","phone","latitude","longitude","is_default"},
     *             @OA\Property(property="name", type="string", example="Home Address"),
     *             @OA\Property(property="street", type="string", example="123 Main Street"),
     *             @OA\Property(property="city", type="string", example="Riyadh"),
     *             @OA\Property(property="phone", type="string", example="966555555555"),
     *             @OA\Property(property="latitude", type="number", format="float", example=42.702279),
     *             @OA\Property(property="longitude", type="number", format="float", example=-35.145415),
     *             @OA\Property(property="is_default", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Success"),
     *             @OA\Property(property="data", ref="#/components/schemas/AddressResource")
     *         )
     *     )
     *     ,
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized"),
     *             @OA\Property(property="errors", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="name", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="street", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="city", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="phone", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="latitude", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="longitude", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="is_default", type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     )
     * )
     */
    public function store(StoreAddressRequest $request): JsonResponse
    {
        $address = $this->addressService->createAddress(Auth::user(), $request->validated());

        return $this->successResponse(
            new AddressResource($address),
            __('messages.success'),
            Response::HTTP_CREATED
        );
    }

    /**
     * Update the specified address.
     *
     * @param UpdateAddressRequest $request
     * @param Address $address
     *
     * @return JsonResponse
     *
     * @throws UserException
     *
     * @OA\Put(
     *     path="/addresses/{address}",
     *     summary="Update address",
     *     description="Update the specified address",
     *     security={{"bearerAuth":{}}},
     *     tags={"Address"},
     *     @OA\Parameter(
     *         name="address",
     *         in="path",
     *         required=true,
     *         description="Address ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="Home Address"),
     *             @OA\Property(property="street", type="string", example="123 Main Street"),
     *             @OA\Property(property="city", type="string", example="Riyadh"),
     *             @OA\Property(property="phone", type="string", example="966555555555"),
     *             @OA\Property(property="latitude", type="number", format="float", example=42.702279),
     *             @OA\Property(property="longitude", type="number", format="float", example=-35.145415),
     *             @OA\Property(property="is_default", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Success"),
     *             @OA\Property(property="data", ref="#/components/schemas/AddressResource")
     *         )
     *     )
     *     ,
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized"),
     *             @OA\Property(property="errors", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Address not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Not Found"),
     *             @OA\Property(property="errors", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="name", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="street", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="city", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="phone", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="latitude", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="longitude", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="is_default", type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     )
     * )
     */
    public function update(UpdateAddressRequest $request, Address $address): JsonResponse
    {
        $this->authorize('update', $address);

        $address = $this->addressService->updateAddress(Auth::user(), $address, $request->validated());

        return $this->successResponse(
            new AddressResource($address),
            __('messages.success'),
            Response::HTTP_OK
        );
    }

    /**
     * Remove the specified address.
     *
     * @param Address $address
     *
     * @return JsonResponse
     *
 * @throws UserException
     *
     * @OA\Delete(
     *     path="/addresses/{address}",
     *     summary="Delete address",
     *     description="Delete the specified address",
     *     security={{"bearerAuth":{}}},
     *     tags={"Address"},
     *     @OA\Parameter(
     *         name="address",
     *         in="path",
     *         required=true,
     *         description="Address ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Success"),
     *             @OA\Property(property="data", nullable=true, type="object", example=null)
     *         )
     *     )
     *     ,
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized"),
     *             @OA\Property(property="errors", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Address not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Not Found"),
     *             @OA\Property(property="errors", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Cannot delete last address",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Cannot delete last address"),
     *             @OA\Property(property="errors", nullable=true)
     *         )
     *     )
     * )
     */
    public function destroy(Address $address): JsonResponse
    {
        $this->authorize('delete', $address);

        $this->addressService->deleteAddress(Auth::user(), $address);

        return $this->successResponse(
            message: __('messages.success'),
            statusCode: Response::HTTP_OK
        );
    }
}
