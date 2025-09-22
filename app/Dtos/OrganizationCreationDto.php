<?php

namespace App\Dtos;

use Illuminate\Http\Request;

final readonly class OrganizationCreationDto
{
    /**
     * OrganizationCreationDto constructor.
     */
    public function __construct(
        public readonly ?string $name,
        public readonly ?string $taxNumber,
        public readonly ?string $commercialRegisterNumber,
    ) {}

    /**
     * Create a new OrganizationCreationData instance from a request.
     */
    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->get('name'),
            taxNumber: $request->get('tax_number'),
            commercialRegisterNumber: $request->get('commercial_register_number'),
        );
    }

    /**
     * Convert DTO to array format for database operations.
     * Only includes non-null values for updates.
     */
    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'tax_number' => $this->taxNumber,
            'commercial_register_number' => $this->commercialRegisterNumber,
        ], fn ($value) => $value !== null);
    }
}
