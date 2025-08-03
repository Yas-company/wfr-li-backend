<?php

namespace App\Http\Controllers\api\v1\Organization\Buyer;

use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Dtos\OrganizationCreationDto;
use App\Services\OrganizationService;
use App\Http\Requests\Organization\StoreRequest;
use App\Http\Resources\Organization\OrganizationResource;

class OrganizationController extends Controller
{
    use ApiResponse;

    /**
     * OrganizationController constructor.
     *
     * @param OrganizationService $organizationService
     */
    public function __construct(protected OrganizationService $organizationService)
    {
        //
    }

    /**
     * Store a new organization.
     *
     * @param StoreRequest $request
     *
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $user = auth()->user();
        $organizationCreationDto = OrganizationCreationDto::fromRequest($request);

        $organization = $this->organizationService->createOrganization($organizationCreationDto, $user);

        return $this->createdResponse(OrganizationResource::make($organization->load(['owner', 'users'])));
    }
}
