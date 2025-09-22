<?php

namespace App\Http\Controllers\api\v1\Supplier;

use App\Http\Requests\GetBuyersRequest;
use App\Http\Resources\BuyerResource;
use App\Services\BuyerService;
use App\Traits\ApiResponse;
use Symfony\Component\HttpFoundation\Response;

class GetBuyersController
{
    use ApiResponse;

    public function __invoke(GetBuyersRequest $request, BuyerService $buyerService)
    {
        $data = $request->validated();
        $result = $buyerService->getRelatedBuyers($data, auth()->user());

        return $this->paginatedResponse(
            $result,
            BuyerResource::collection($result),
            __('messages.buyers.retrieved_successfully'),
            Response::HTTP_OK
        );
    }
}
