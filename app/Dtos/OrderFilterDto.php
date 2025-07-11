<?php

namespace App\Dtos;

use Carbon\Carbon;
use Illuminate\Http\Request;

final readonly class OrderFilterDto
{
    /**
     * OrderFilterDto constructor.
     *
     * @param string|null $order_status
     * @param string|null $shipping_method
     * @param Carbon|null $start_date
     * @param Carbon|null $end_date
     */
    public function __construct(
        public readonly ?string $order_status = null,
        public readonly ?string $shipping_method = null,
        public readonly ?Carbon $start_date = null,
        public readonly ?Carbon $end_date = null,
    )
    {
        //
    }

    /**
     * Create a new OrderFilterDto instance from a request.
     *
     * @param Request $request
     *
     * @return OrderFilterDto
     */
    public static function fromRequest(Request $request): self
    {
        return new self(
            order_status: $request->get('status'),
            shipping_method: $request->get('shipping_method'),
            start_date: $request->date('start_date'),
            end_date: $request->date('end_date'),
        );
    }
}
