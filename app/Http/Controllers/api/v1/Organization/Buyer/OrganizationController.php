<?php

namespace App\Http\Controllers\api\v1\Organization\Buyer;

use App\Dtos\OrganizationCreationDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\StoreRequest;
use App\Http\Requests\Organization\UpdateRequest;
use App\Http\Resources\Organization\OrganizationResource;
use App\Models\Organization;
use App\Services\OrganizationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

/**
 * @OA\Tag(
 *     name="Organization",
 *     description="Organization endpoints"
 * )
 */
class OrganizationController extends Controller
{
    use ApiResponse;

    /**
     * OrganizationController constructor.
     */
    public function __construct(protected OrganizationService $organizationService)
    {
        //
    }

    /**
     * Store a new organization.
     *
     * @OA\Post(
     *     path="/buyer/organizations",
     *     summary="Create a new organization",
     *     description="Create a new organization for the authenticated buyer user",
     *     tags={"Organization"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"name", "tax_number", "commercial_register_number"},
     *
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 description="Organization name (must be unique among approved organizations)",
     *                 example="ABC Company Ltd",
     *                 maxLength=255
     *             ),
     *             @OA\Property(
     *                 property="tax_number",
     *                 type="string",
     *                 description="Tax number (must start with 3 and be exactly 16 digits, unique among approved organizations)",
     *                 example="3123456789012345",
     *                 pattern="^3[0-9]{15}$"
     *             ),
     *             @OA\Property(
     *                 property="commercial_register_number",
     *                 type="string",
     *                 description="Commercial register number (must be exactly 7 digits, unique among approved organizations)",
     *                 example="1234567",
     *                 pattern="^[0-9]{7}$"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Organization created successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Organization created successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/OrganizationResource"
     *             ),
     *             @OA\Property(property="status_code", type="integer", example=201)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="name", type="array", @OA\Items(type="string", example="The name field is required")),
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="The email field is required"))
     *             ),
     *             @OA\Property(property="status_code", type="integer", example=400)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing authentication token",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated"),
     *             @OA\Property(property="status_code", type="integer", example=401)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity - Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The given data was invalid"),
     *             @OA\Property(property="status_code", type="integer", example=422)
     *         )
     *     )
     * )
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $user = auth()->user();
        $organizationCreationDto = OrganizationCreationDto::fromRequest($request);

        $organization = $this->organizationService->createOrganization($organizationCreationDto, $user);

        return $this->createdResponse(OrganizationResource::make($organization->load(['owner', 'users'])));
    }

    /**
     * Check organization.
     */
    public function checkOrganization(): JsonResponse
    {

        $result = $this->organizationService->checkOrganization(auth()->user());

        return $this->successResponse(new OrganizationResource($result));
    }

    /**
     * update organization.
     */
    public function update(UpdateRequest $request, Organization $organization): JsonResponse
    {
        $organizationUpdateDto = OrganizationCreationDto::fromRequest($request);
        $organization = $this->organizationService->updateOrganization($organization, data: $organizationUpdateDto, user: auth()->user());

        return $this->successResponse(new OrganizationResource($organization));
    }
}
