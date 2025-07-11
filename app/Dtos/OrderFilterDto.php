<?php

namespace App\Dtos;


use Carbon\Carbon;
use Illuminate\Http\Request;

final readonly class OrderFilterDto
{
    /**
     * OrderFilterDto constructor.
     *
     * @param string|null $orderStatus
     * @param string|null $shippingMethod
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     */
    public function __construct(
        public readonly ?string $orderStatus = null,
        public readonly ?string $shippingMethod = null,
        public readonly ?Carbon $startDate = null,
        public readonly ?Carbon $endDate = null,
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
            orderStatus: $request->get('status'),
            shippingMethod: $request->get('shipping_method'),
            startDate: $request->date('start_date'),
            endDate: $request->date('end_date'),
        );
    }
}
