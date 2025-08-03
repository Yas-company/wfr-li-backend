<?php

namespace App\Dtos;

use Illuminate\Http\Request;

final readonly class OrganizationCreationDto
{
    /**
     * OrderFilterDto constructor.
     *
     * @param string $name
     * @param string $taxNumber
     * @param string $commercialRegisterNumber
     */
    public function __construct(
        public readonly string $name,
        public readonly string $taxNumber,
        public readonly string $commercialRegisterNumber,
    ) {
    }

    /**
     * Create a new OrganizationCreationData instance from a request.
     *
     * @param Request $request
     *
     * @return OrganizationCreationDto
     */
    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->get('name'),
            taxNumber: $request->get('tax_number'),
            commercialRegisterNumber: $request->get('commercial_register_number'),
        );
    }
}
