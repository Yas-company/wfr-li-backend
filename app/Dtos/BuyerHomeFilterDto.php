<?php

namespace App\Dtos;

use Illuminate\Http\Request;

class BuyerHomeFilterDto
{
    public function __construct(
        public readonly ?int $categoryId = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            categoryId: $request->get('category_id'),
        );
    }

    public function hasCategoryFilter(): bool
    {
        return $this->categoryId !== null;
    }
}
