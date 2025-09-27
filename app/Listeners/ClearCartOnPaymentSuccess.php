<?php

namespace App\Listeners;

use App\Events\PaymentSuccessful;
use App\Services\Contracts\CartServiceInterface;

class ClearCartOnPaymentSuccess
{
    /**
     * Create the event listener.
     */
    public function __construct(private CartServiceInterface $cartService)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PaymentSuccessful $event): void
    {
        $this->cartService->clearCart($event->user);
    }
}
