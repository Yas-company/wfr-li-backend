<?php

namespace App\Http\Controllers\api\v1;

use App\Models\Product;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\OrderResource;
use Symfony\Component\HttpFoundation\Response;
use App\Services\Contracts\CartServiceInterface;
use App\Http\Requests\Cart\AddProductToCartRequest;
use App\Http\Requests\Cart\CheckoutCartRequest;

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

    /**
     * Checkout the cart.
     *
     * @param CheckoutCartRequest $request
     *
     * @return JsonResponse
     */
    public function checkout(CheckoutCartRequest $request): JsonResponse
    {
        $data = [
            'shipping_address' => $request->validated('shipping_address'),
            'shipping_latitude' => $request->validated('shipping_latitude'),
            'shipping_longitude' => $request->validated('shipping_longitude'),
            'payment_method' => $request->validated('payment_method'),
            'notes' => $request->validated('notes'),
        ];

        try {
            $order = $this->cartService->checkout(Auth::user(), $data);
            return $this->successResponse(
                data: ['order' => OrderResource::make($order)],
                statusCode: Response::HTTP_CREATED
            );
        } catch (\App\Exceptions\CartException $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
