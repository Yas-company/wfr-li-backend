<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Requests\PageSlugRequest;
use App\Http\Resources\PageResource;
use OpenApi\Annotations as OA;
class PageController extends Controller
{
    use ApiResponse;

    /**
     * get a page by slug
     *
     * @OA\Get(
     *     path="/api/v1/pages/{slug}",
     *     summary="Get a page by slug",
     *     tags={"Pages"},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="Accept-Language",
     *         in="header",
     *         required=false,
     *         description="Localization header (en or ar)",
     *         @OA\Schema(type="string", enum={"en","ar"})
     *     ),
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Page fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="slug", type="string"),
     *                 @OA\Property(property="content", type="string")
     *             ),
     *             example={
     *                 "success": true,
     *                 "message": "Page fetched successfully",
     *                 "data": {
     *                     "id": 2,
     *                     "title": "Privacy Policy",
     *                     "slug": "privacy-policy",
     *                     "content": "<h2>Privacy Policy</h2><p>...</p>"
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(example={"message": "Unauthenticated."})
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(example={
     *             "message": "The given data was invalid.",
     *             "errors": {"slug": {"The slug is not found"}}
     *         })
     *     )
     * )
     */
    public function show(PageSlugRequest $request)
    {
        $page = Page::where('slug', $request->slug)->isActive()->first();
        return $this->successResponse(new PageResource($page), __('messages.page.fetched_successfully'));
    }
}
