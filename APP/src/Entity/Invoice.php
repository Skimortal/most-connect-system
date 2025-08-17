<?php

namespace App\Entity;

use App\Enum\InvoiceStatus;
use App\Repository\InvoiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Invoice extends Base
{

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Company $company;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Customer $customer;

    #[ORM\OneToMany(mappedBy: 'invoice', targetEntity: InvoiceItem::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $invoiceItems;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $invoiceNumber = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $invoiceDate;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private float $taxSum = 0.00;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private float $totalNetto = 0.00;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $total = 0.00;

    #[ORM\Column(enumType: InvoiceStatus::class)]
    private InvoiceStatus $status = InvoiceStatus::OFFEN;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $entryText = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $paymentInfoText = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $bottomText = null;

    public function __construct()
    {
        $this->invoiceItems = new ArrayCollection();
        $this->invoiceDate = new \DateTimeImmutable();
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function setCompany(Company $company): void
    {
        $this->company = $company;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function getInvoiceItems(): Collection
    {
        return $this->invoiceItems;
    }

    public function setInvoiceItems(Collection $invoiceItems): void
    {
        $this->invoiceItems = $invoiceItems;
    }

    public function addInvoiceItem(InvoiceItem $item): self
    {
        if (!$this->invoiceItems->contains($item)) {
            $this->invoiceItems->add($item);
            $item->setInvoice($this);
        }
        return $this;
    }

    public function removeInvoiceItem(InvoiceItem $item): self
    {
        if ($this->invoiceItems->removeElement($item)) {
            if ($item->getInvoice() === $this) {
                $item->setInvoice(null);
            }
        }
        return $this;
    }

    public function getInvoiceNumber(): ?string
    {
        return $this->invoiceNumber ?? null;
    }

    public function setInvoiceNumber(string $invoiceNumber): void
    {
        if ($this->invoiceNumber !== null) {
            throw new \LogicException('Die Rechnungsnummer kann nicht mehr geändert werden.');
        }

        $this->invoiceNumber = $invoiceNumber;
    }

    public function getInvoiceDate(): \DateTimeInterface
    {
        return $this->invoiceDate;
    }

    public function setInvoiceDate(\DateTimeInterface $invoiceDate): void
    {
        $this->invoiceDate = $invoiceDate;
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

    public function calcInvoiceTotalNetto(): float
    {
        return array_sum($this->invoiceItems->map(fn(InvoiceItem $i) => $i->calcLineTotalNetto())->toArray());
    }

    public function calcInvoiceTaxSum(): float
    {
        return array_sum($this->invoiceItems->map(fn(InvoiceItem $i) => $i->calcTaxSum())->toArray());
    }

    public function calcInvoiceTotal(): float
    {
        return array_sum($this->invoiceItems->map(fn(InvoiceItem $i) => $i->calcLineTotal())->toArray());
    }

    public function setAllPrices(): void
    {
        $this->invoiceItems->map(fn(InvoiceItem $i) => $i->setAllPrices());
        $this->setTotalNetto($this->calcInvoiceTotalNetto());
        $this->setTaxSum($this->calcInvoiceTaxSum());
        $this->setTotal($this->calcInvoiceTotal());
    }

    public function getStatus(): InvoiceStatus
    {
        return $this->status;
    }

    public function setStatus(InvoiceStatus $status): void
    {
        $this->status = $status;
    }

    public function getEntryText(): ?string
    {
        return $this->entryText;
    }

    public function setEntryText(?string $entryText): void
    {
        $this->entryText = $entryText;
    }

    public function getPaymentInfoText(): ?string
    {
        return $this->paymentInfoText;
    }

    public function setPaymentInfoText(?string $paymentInfoText): void
    {
        $this->paymentInfoText = $paymentInfoText;
    }

    public function getBottomText(): ?string
    {
        return $this->bottomText;
    }

    public function setBottomText(?string $bottomText): void
    {
        $this->bottomText = $bottomText;
    }

}
