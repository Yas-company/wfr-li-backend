<?php

namespace App\Http\Controllers\api\v1;

use App\Models\Product;
use App\Traits\ApiResponse;
use App\Dtos\CartCheckoutDto;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\OrderResource;
use App\Services\Payment\PaymentService;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Cart\CheckoutCartRequest;
use App\Services\Contracts\CartServiceInterface;
use App\Http\Requests\Cart\AddProductToCartRequest;
use OpenApi\Annotations as OA;

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
	 *
	 * @OA\Get(
	 *     path="/cart",
	 *     summary="Get current cart",
	 *     description="Retrieve the authenticated user's cart with totals and supplier requirements",
	 *     security={{"bearerAuth":{}}},
	 *     tags={"Cart"},
	 *     @OA\Response(
	 *         response=200,
	 *         description="Successful operation",
	 *         @OA\JsonContent(
	 *             type="object",
	 *             @OA\Property(property="success", type="boolean", example=true),
	 *             @OA\Property(property="message", type="string", example="Success"),
	 *             @OA\Property(property="data", type="object",
	 *                 @OA\Property(property="cart", type="object", ref="#/components/schemas/Cart"),
	 *                 @OA\Property(property="total", type="number", format="float", example=300.5),
	 *                 @OA\Property(property="total_discount", type="number", format="float", example=25.25),
	 *                 @OA\Property(property="total_products", type="integer", example=3),
	 *                 @OA\Property(
	 *                     property="supplier_requirements",
	 *                     type="array",
	 *                     @OA\Items(type="object",
	 *                         @OA\Property(property="supplier_id", type="integer", example=12),
	 *                         @OA\Property(property="supplier_name", type="string", example="Acme Co"),
	 *                         @OA\Property(property="supplier_image", type="string", nullable=true, example="/storage/users/1.jpg"),
	 *                         @OA\Property(property="required_amount", type="number", format="float", example=500),
	 *                         @OA\Property(property="current_total", type="number", format="float", example=300)
	 *                     )
	 *                 )
	 *             )
	 *         )
	 *     ),
	 *     @OA\Response(response=401, description="Unauthorized")
	 * )
     */
    public function index(): JsonResponse
    {
        $cart = $this->cartService->getCart(Auth::user());
        $totals = $this->cartService->getCartTotals($cart);
        $supplierRequirements = $this->cartService->getSupplierRequirements($cart);

        return $this->successResponse(
            data:[
                'cart' => CartResource::make($cart),
                'total' => $totals->total,
                'total_discount' => $totals->discount,
                'total_products' => $totals->totalProducts,
                'supplier_requirements' => $supplierRequirements,
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
	 *
	 * @OA\Post(
	 *     path="/cart",
	 *     summary="Add product to cart",
	 *     description="Add a product to the authenticated user's cart",
	 *     security={{"bearerAuth":{}}},
	 *     tags={"Cart"},
	 *     @OA\RequestBody(
	 *         required=true,
	 *         @OA\JsonContent(
	 *             type="object",
	 *             required={"product_id", "quantity"},
	 *             @OA\Property(property="product_id", type="integer", example=123),
	 *             @OA\Property(property="quantity", type="integer", minimum=1, example=2)
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=201,
	 *         description="Product added",
	 *         @OA\JsonContent(
	 *             type="object",
	 *             @OA\Property(property="success", type="boolean", example=true),
	 *             @OA\Property(property="message", type="string", example="Success"),
	 *             @OA\Property(property="data", type="array", @OA\Items())
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=401,
	 *         description="Unauthorized"
	 *     ),
	 *     @OA\Response(
	 *         response=422,
	 *         description="Validation error",
	 *         @OA\JsonContent(
	 *             type="object",
	 *             @OA\Property(property="success", type="boolean", example=false),
	 *             @OA\Property(property="message", type="string", example="The given data was invalid."),
	 *             @OA\Property(property="errors", type="object",
	 *                 @OA\Property(property="product_id", type="array", @OA\Items(type="string", example="The selected product_id is invalid.")),
	 *                 @OA\Property(property="quantity", type="array", @OA\Items(type="string", example="The quantity must be at least 1."))
	 *             )
	 *         )
	 *     )
	 * )
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
	 *
	 * @OA\Delete(
	 *     path="/cart/{product}",
	 *     summary="Remove product from cart",
	 *     description="Remove a product from the authenticated user's cart",
	 *     security={{"bearerAuth":{}}},
	 *     tags={"Cart"},
	 *     @OA\Parameter(
	 *         name="product",
	 *         in="path",
	 *         required=true,
	 *         description="Product ID",
	 *         @OA\Schema(type="integer")
	 *     ),
	 *     @OA\Response(
	 *         response=201,
	 *         description="Removed",
	 *         @OA\JsonContent(
	 *             type="object",
	 *             @OA\Property(property="success", type="boolean", example=true),
	 *             @OA\Property(property="message", type="string", example="Success"),
	 *             @OA\Property(property="data", type="array", @OA\Items())
	 *         )
	 *     ),
	 *     @OA\Response(response=401, description="Unauthorized"),
	 *     @OA\Response(response=404, description="Product not found")
	 * )
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
	 *
	 * @OA\Put(
	 *     path="/cart/clear",
	 *     summary="Clear cart",
	 *     description="Remove all products from the authenticated user's cart",
	 *     security={{"bearerAuth":{}}},
	 *     tags={"Cart"},
	 *     @OA\Response(
	 *         response=201,
	 *         description="Cleared",
	 *         @OA\JsonContent(
	 *             type="object",
	 *             @OA\Property(property="success", type="boolean", example=true),
	 *             @OA\Property(property="message", type="string", example="Success"),
	 *             @OA\Property(property="data", type="array", @OA\Items())
	 *         )
	 *     ),
	 *     @OA\Response(response=401, description="Unauthorized")
	 * )
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
	 *
	 * @OA\Post(
	 *     path="/cart/checkout",
	 *     summary="Checkout cart",
	 *     description="Checkout the authenticated user's cart",
	 *     security={{"bearerAuth":{}}},
	 *     tags={"Cart"},
	 *     @OA\RequestBody(
	 *         required=true,
	 *         @OA\JsonContent(
	 *             type="object",
	 *             required={"shipping_address_id", "payment_method", "shipping_method", "order_type"},
	 *             @OA\Property(property="shipping_address_id", type="integer", example=10),
	 *             @OA\Property(property="payment_method", type="integer", enum={1,2}, example=1, description="1: CASH_ON_DELIVERY, 2: Tap"),
	 *             @OA\Property(property="notes", type="string", nullable=true, example="Please deliver between 9am-5pm"),
	 *             @OA\Property(property="shipping_method", type="integer", enum={1,2}, example=1, description="1: DELEGATE, 2: PICKUP"),
	 *             @OA\Property(property="order_type", type="integer", enum={1,2}, example=1, description="1: INDIVIDUAL, 2: ORGANIZATION")
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=201,
	 *         description="Order created",
	 *         @OA\JsonContent(
	 *             type="object",
	 *             @OA\Property(property="success", type="boolean", example=true),
	 *             @OA\Property(property="message", type="string", example="Success"),
	 *             @OA\Property(property="data", type="object",
	 *                 @OA\Property(property="order", ref="#/components/schemas/Order")
	 *             )
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=400,
	 *         description="Cart validation failed",
	 *         @OA\JsonContent(
	 *             type="object",
	 *             @OA\Property(property="success", type="boolean", example=false),
	 *             @OA\Property(property="message", type="string", example="Minimum order amount not met"),
	 *             @OA\Property(property="errors", nullable=true)
	 *         )
	 *     ),
	 *     @OA\Response(response=401, description="Unauthorized"),
	 *     @OA\Response(
	 *         response=422,
	 *         description="Validation error",
	 *         @OA\JsonContent(
	 *             type="object",
	 *             @OA\Property(property="success", type="boolean", example=false),
	 *             @OA\Property(property="message", type="string", example="The given data was invalid."),
	 *             @OA\Property(property="errors", type="object",
	 *                 @OA\Property(property="shipping_address_id", type="array", @OA\Items(type="string")),
	 *                 @OA\Property(property="payment_method", type="array", @OA\Items(type="string")),
	 *                 @OA\Property(property="shipping_method", type="array", @OA\Items(type="string")),
	 *                 @OA\Property(property="order_type", type="array", @OA\Items(type="string"))
	 *             )
	 *         )
	 *     )
	 * )
     */
    public function checkout(CheckoutCartRequest $request, PaymentService $paymentService): JsonResponse
    {
        $cartCheckoutDto = CartCheckoutDto::fromRequest($request);

        try {
            $data = $this->cartService->checkout(Auth::user(), $cartCheckoutDto, $paymentService);
            return $this->successResponse(
                data: [
                    'order' => OrderResource::make($data['order']),
                    'payment_url' => $data['payment_url']
                ],
                statusCode: Response::HTTP_CREATED
            );
        } catch (\App\Exceptions\CartException $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
