<?php

namespace App\Http\Controllers\api\v1;

use App\Models\Product;
use App\Traits\ApiResponse;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Cart\AddProductToCartRequest;

class CartController extends Controller
{
    use ApiResponse;

    /**
     * CarController constructor.
     *
     * @param CartService $cartService
     */
    public function __construct(protected CartService $cartService)
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
        $this->cartService->getCart(Auth::user());

        return $this->cartResponse(Response::HTTP_OK);
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
            $request->product_id,
            $request->quantity
        );

        return $this->cartResponse(Response::HTTP_CREATED);
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

        return $this->cartResponse(Response::HTTP_OK);
    }

    /**
     * Clear all products from the cart.
     *
     * @return JsonResponse
     */
    public function clear(): JsonResponse
    {
        $this->cartService->clearCart(Auth::user());

        return $this->cartResponse(Response::HTTP_OK);
    }

    /**
     * Build the cart response with total and discount.
     *
     * @param int $statusCode
     *
     * @return JsonResponse
     */
    private function cartResponse(int $statusCode): JsonResponse
    {
        $cart = $this->cartService->getCart(Auth::user());
        $total = $this->cartService->getCartTotal(Auth::user());
        $totalBeforeDiscount = $this->cartService->getCartTotalBeforeDiscount(Auth::user());

        return $this->successResponse(
            data:[
                'cart' => CartResource::make($cart),
                'total' => $total,
                'total_discount' => $totalBeforeDiscount - $total,
            ],
            statusCode: $statusCode
        );
    }
}
