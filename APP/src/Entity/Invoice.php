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

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $total = 0.00;

    #[ORM\Column(enumType: InvoiceStatus::class)]
    private InvoiceStatus $status = InvoiceStatus::OFFEN;

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

    public function getTotal(): float
    {
        return $this->total;
    }

    public function setTotal(float $total): void
    {
        $this->total = $total;
    }

    public function calcInvoiceTotal(): float
    {
        return array_sum($this->invoiceItems->map(fn(InvoiceItem $i) => $i->calcLineTotal())->toArray());
    }

    public function getStatus(): InvoiceStatus
    {
        return $this->status;
    }

    public function setStatus(InvoiceStatus $status): void
    {
        $this->status = $status;
    }

}
