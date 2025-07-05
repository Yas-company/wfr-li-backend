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
     */
    public function update(UpdateAddressRequest $request, Address $address): JsonResponse
    {
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
     */
    public function destroy(Address $address): JsonResponse
    {
        $this->addressService->deleteAddress(Auth::user(), $address);

        return $this->successResponse(
            message: __('messages.success'),
            statusCode: Response::HTTP_OK
        );
    }
}
