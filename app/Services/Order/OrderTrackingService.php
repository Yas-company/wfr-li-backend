<?php

namespace App\Services\Order;

class OrderTrackingService
{
    /**
     * Generate a unique 8-digit tracking number for an order based on user_id, supplier_id, and order_id.
     *
     * @param string $userId
     * @param string $supplierId
     * @param string $orderId
     * @return string
     */
    public function generateTrackingNumber(string $userId, string $supplierId, string $orderId): string
    {
        $combinedString = $userId . $supplierId . $orderId;

        $hash = md5($combinedString);
        $numericHash = hexdec(substr($hash, 0, 8));

        return sprintf("%08d", $numericHash % 100000000);
    }
}
