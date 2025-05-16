<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = $request->user();
        $product = Product::findOrFail($request->product_id);

        return DB::transaction(function () use ($user, $product, $request) {
            // Get or create cart
            $cart = $user->cart ?? $user->cart()->create(['total_amount' => 0]);

            // Check if product already exists in cart
            $cartItem = $cart->items()
                ->where('product_id', $product->id)
                ->first();

            if ($cartItem) {
                // Update the quantity and price
                $newQuantity = $cartItem->quantity + $request->quantity;
                $cartItem->update([
                    'quantity' => $newQuantity,
                    'price' => $product->price,
                ]);
            } else {
                // Create new cart item
                $cartItem = $cart->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $request->quantity,
                    'price' => $product->price,
                ]);
            }

            // Update cart total
            $this->updateCartTotal($cart);

            // Return fresh data
            return response()->json([
                'message' => __('messages.cart.added'),
                'cart' => $cart->fresh()->load('items.product'),
            ]);
        });
    }

    public function removeFromCart(Request $request, CartItem $cartItem)
    {
        $user = $request->user();

        if ($cartItem->cart->user_id !== $user->id) {
            return response()->json(['message' => __('messages.unauthorized')], 403);
        }

        return DB::transaction(function () use ($cartItem) {
            $cart = $cartItem->cart;
            $cartItem->delete();

            $this->updateCartTotal($cart);

            return response()->json([
                'message' => __('messages.cart.removed'),
                'cart' => $cart->load('items.product'),
            ]);
        });
    }

    public function updateQuantity(Request $request, CartItem $cartItem)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $user = $request->user();

        if ($cartItem->cart->user_id !== $user->id) {
            return response()->json(['message' => __('messages.unauthorized')], 403);
        }

        return DB::transaction(function () use ($cartItem, $request) {
            $cart = $cartItem->cart;

            $cartItem->update([
                'quantity' => $request->quantity,
            ]);

            $this->updateCartTotal($cart);

            return response()->json([
                'message' => __('messages.cart.updated'),
                'cart' => $cart->load('items.product'),
            ]);
        });
    }

    public function getCart(Request $request)
    {
        $user = $request->user();
        $cart = $user->cart;

        if (!$cart) {
            return response()->json([
                'message' => __('messages.cart.empty'),
                'cart' => null,
            ]);
        }

        // Calculate total amount from items
        $this->updateCartTotal($cart);

        return response()->json([
            'cart' => $cart->load('items.product'),
        ]);
    }

    private function updateCartTotal(Cart $cart)
    {
        $total = $cart->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $cart->update(['total_amount' => $total]);
    }
}
