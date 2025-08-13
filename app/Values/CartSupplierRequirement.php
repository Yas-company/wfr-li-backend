<?php

namespace App\Values;

class CartSupplierRequirement
{

    /**
     * CartSupplierRequirement constructor.
     *
     * @param int $supplierId
     * @param string $supplierName
     * @param float $requiredAmount
     * @param float $currentTotal
     *
     */
    public function __construct(
        public int $supplierId,
        public string $supplierName,
        public float $requiredAmount,
        public float $currentTotal,
        public ?string $supplierImage = null,
    ) {}

    /**
     * Get the residual amount.
     *
     * @return float
     */
    public function residualAmount(): float
    {
        return max(0, $this->requiredAmount - $this->currentTotal);
    }

    /**
     * Get the array representation of the object.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'supplier_id' => $this->supplierId,
            'supplier_name' => $this->supplierName,
            'supplier_image' => $this->supplierImage ? asset('storage/'.$this->supplierImage) : null,
            'required_amount' => $this->requiredAmount,
            'current_total' => $this->currentTotal,
            'residual_amount' => $this->residualAmount(),
        ];
    }
}
