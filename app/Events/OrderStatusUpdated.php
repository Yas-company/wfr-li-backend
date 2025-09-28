<?php

namespace App\Events;

use App\Models\Order;
use App\Enums\Order\OrderStatus;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Order $order, public OrderStatus $newStatus)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): Channel
    {
        return new PrivateChannel("buyer.{$this->order->user_id}");
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'status' => $this->newStatus->value,
            'user_id' => $this->order->user_id,
            'updated_at' => $this->order->updated_at->toDateTimeString(),
        ];
    }

    /**
     * Get the event name.
     */
    public function broadcastAs(): string
    {
        return 'order.status.updated';
    }
}
