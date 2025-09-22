<?php

namespace App\Dtos;

use Illuminate\Http\Request;

final readonly class OrderChartDto
{
    /**
     * OrderChartDto constructor.
     *
     * @param string $timeFilter
     * @param string|null $status
     */
    public function __construct(
        public readonly string $timeFilter = 'monthly',
        public readonly ?string $status = null,
    )
    {
        //
    }

    /**
     * Create a new OrderChartDto instance from a request.
     *
     * @param Request $request
     *
     * @return OrderChartDto
     */
    public static function fromRequest(Request $request): self
    {
        return new self(
            timeFilter: $request->input('time_filter', 'monthly'),
            status: $request->input('status'),
        );
    }
}
