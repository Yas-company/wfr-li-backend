<?php

namespace App\Http\Controllers\api\v1\Buyer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Buyer\BuyerImageRequest;
use App\Http\Requests\Buyer\UpdateBuyerRequest;
use App\Http\Resources\Buyer\BuyerUpdatedResource;
use App\Http\Services\OtpService;
use App\Services\Buyer\BuyerProfileService;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class BuyerProfileController extends Controller
{
    use ApiResponse;

    /**
     * Summary of __construct
     */
    public function __construct(private BuyerProfileService $buyerProfileService) {}

    /**
     * Summary of updateBuyerProfile
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateBuyerProfile(UpdateBuyerRequest $request, OtpService $otpService)
    {

        $data = $request->validated();
        if (isset($data['phone']) && $data['phone'] !== Auth::user()->phone) {

            $isValid = $otpService->isVerified($data['phone']);

            if (! $isValid) {
                return $this->errorResponse(
                    message: __('messages.invalid_otp'),
                    statusCode: Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }
            $data['is_verified'] = true;
        }
        $buyer = $this->buyerProfileService->updateBuyerProfile($data, Auth::user());

        return $this->successResponse(
            data: new BuyerUpdatedResource($buyer),
            message: __('messages.buyer.profile_updated'),
            statusCode: Response::HTTP_OK
        );
    }

    /**
     * Summary of changeBuyerImage
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeBuyerImage(BuyerImageRequest $request)
    {
        $data = $request->validated();
        $buyer = $this->buyerProfileService->changeBuyerImage($data, Auth::user());

        return $this->successResponse(
            data: new BuyerUpdatedResource($buyer),
            message: __('messages.buyer.image_updated'),
            statusCode: Response::HTTP_OK
        );
    }
}
