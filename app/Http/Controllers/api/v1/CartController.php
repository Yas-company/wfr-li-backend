<?php

namespace App\Http\Controllers\api\v1;

use App\Models\Product;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Cart\AddProductToCartRequest;
use App\Services\Contracts\CartServiceInterface;

class CartController extends Controller
{
    use ApiResponse;

    /**
     * CartController constructor.
     *
     * @param CartServiceInterface $cartService
     */
    public function __construct(protected CartServiceInterface $cartService)
    {
        //
    }

    /**
     * Display the current user's cart and totals.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $cart = $this->cartService->getCart(Auth::user());
        $totals = $this->cartService->getCartTotals($cart);

        return $this->successResponse(
            data:[
                'cart' => CartResource::make($cart),
                'total' => $totals->total,
                'total_discount' => $totals->discount,
            ],
            statusCode: Response::HTTP_OK
        );
    }

    /**
     * Add a product to the cart.
     *
     * @param AddProductToCartRequest $request
     *
     * @return JsonResponse
     */
    public function store(AddProductToCartRequest $request): JsonResponse
    {
        $this->cartService->addToCart(
            Auth::user(),
            $request->validated('product_id'),
            $request->validated('quantity')
        );

        return $this->successResponse(data:[], statusCode: Response::HTTP_CREATED);
    }

    /**
     * Remove a product from the cart.
     *
     * @param Product $product
     *
     * @return JsonResponse
     */
    public function destroy(Product $product)
    {
        $this->cartService->removeFromCart(Auth::user(), $product->id);

        return $this->successResponse(data:[], statusCode: Response::HTTP_CREATED);
    }

    /**
     * Clear all products from the cart.
     *
     * @return JsonResponse
     */
    public function clear(): JsonResponse
    {
        $this->cartService->clearCart(Auth::user());

        return $this->successResponse(data:[], statusCode: Response::HTTP_CREATED);
    }
}
