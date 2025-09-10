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
        public bool $completed = false,
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
            'required_amount' => money($this->requiredAmount, 2),
            'current_total' => money($this->currentTotal, 2),
            'residual_amount' => money($this->residualAmount(), 2),
            'completed' => $this->residualAmount() <= 0
        ];
    }
}
