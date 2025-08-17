<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[ORM\HasLifecycleCallbacks]
class InvoiceItem extends Base
{

    #[ORM\ManyToOne(inversedBy: 'invoiceItems')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Invoice $invoice = null; // 👈 nullable in PHP

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\Column(length: 255, nullable: true)]
    private string $description;

    #[ORM\Column(type: 'integer')]
    private int $quantity;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $unitPrice;

    #[ORM\ManyToOne(targetEntity: TaxRate::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?TaxRate $taxRate = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $taxSum = 0.0;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $totalNetto = 0.0;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $total = 0.0;

    public function getInvoice(): ?Invoice
    {
        return $this->invoice;
    }

    public function setInvoice(?Invoice $invoice): self
    {
        $this->invoice = $invoice;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getUnitPrice(): float
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(float $unitPrice): void
    {
        $this->unitPrice = $unitPrice;
    }

    public function getTaxRate(): ?TaxRate
    {
        return $this->taxRate;
    }

    public function setTaxRate(?TaxRate $taxRate): void
    {
        $this->taxRate = $taxRate;
    }

    public function getTaxSum(): float
    {
        return $this->taxSum;
    }

    public function setTaxSum(float $taxSum): void
    {
        $this->taxSum = $taxSum;
    }

    public function getTotalNetto(): float
    {
        return $this->totalNetto;
    }

    public function setTotalNetto(float $totalNetto): void
    {
        $this->totalNetto = $totalNetto;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function setTotal(float $total): void
    {
        $this->total = $total;
    }

    public function calcTaxSum(): float
    {
        return ($this->quantity * $this->unitPrice) * ($this->taxRate->getRate() / 100);
    }

    public function calcLineTotalNetto(): float
    {
        return $this->quantity * $this->unitPrice;
    }

    public function calcLineTotal(): float
    {
        return ($this->quantity * $this->unitPrice) * (1 + $this->taxRate->getRate() / 100);
    }

    public function setAllPrices(): void
    {
        $this->setTotalNetto($this->calcLineTotalNetto());
        $this->setTaxSum($this->calcTaxSum());
        $this->setTotal($this->calcLineTotal());
    }

}
