<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BuyerBillMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order->load(['user', 'products.product']);
        $this->subject('Your Order Bill #' . $this->order->id);
    }

    public function build()
    {
        // Build a generic bill payload from the real order
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

        $bill = [
            'order_number' => $this->order->id,
'recipient_name' => $this->order->user->name,
            'email_subject' => 'Your Order Bill #' . $this->order->id,
            'items' => $items,
            // Extend if you store shipping/tax elsewhere
            'totals' => [
                'shipping' => 0,
                'tax' => 0,
            ],
            'cta_url' => null,
            'note' => null,
        ];

        return $this->view('emails.buyer', [
            'bill' => $bill,
        ]);
    }
}


