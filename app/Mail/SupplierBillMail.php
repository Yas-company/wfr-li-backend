<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupplierBillMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order->load(['user', 'products.product', 'orderDetail.shippingAddress']);
        $this->subject('Supplier Bill #' . $this->order->id);
    }

    public function build()
    {
        $items = $this->order->products->map(function ($orderProduct) {
            $product = $orderProduct->product;
            $unitBefore = (float)($product->price_before_discount ?? $product->price ?? $orderProduct->price ?? 0);
            $unitAfter = (float)($orderProduct->price ?? ($product->price ?? 0));

            return [
                'name' => $product->name ?? ('Item #' . $orderProduct->id),
                'quantity' => (int)($orderProduct->quantity ?? 1),
                'unit_price_before' => $unitBefore,
                'unit_price_after' => $unitAfter,
            ];
        })->toArray();

        // Get buyer details and address for supplier reference
        $buyer = $this->order->user;
        $shippingAddress = optional($this->order->orderDetail)->shippingAddress;

        $bill = [
            'order_number' => $this->order->id,
            'recipient_name' => $this->order->supplier->name,
            'email_subject' => 'Supplier Bill #' . $this->order->id,    
            'items' => $items,
            'buyer_details' => [
                'name' => optional($buyer)->name,
                'email' => optional($buyer)->email,
                'phone' => optional($buyer)->phone,
            ],
            'shipping_address' => $shippingAddress ? [
                'name' => $shippingAddress->name,
                'street' => $shippingAddress->street,
                'city' => $shippingAddress->city,
                'phone' => $shippingAddress->phone,
            ] : null,
            'totals' => [
                'shipping' => 0,
                'tax' => 0,
            ],
            // 'mediator_interest' => 0, // put global interest here if needed
            'cta_url' => null,
            'note' => null,
        ];

        return $this->view('emails.supplier', [
            'bill' => $bill,
        ]);
    }
}


