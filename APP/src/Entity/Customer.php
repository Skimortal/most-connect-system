<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[ORM\Table(name: 'customer')]
class Customer extends Base
{
    #[ORM\Column(length: 200)]
    #[Assert\NotBlank]
    private string $companyName;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $vatId = null; // ATU...

    #[ORM\Column(length: 80, nullable: true)]
    private ?string $registrationNumber = null;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $street = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $postalCode = null;

    #[ORM\Column(length: 120, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 120, nullable: true)]
    private ?string $country;

    #[ORM\Column(length: 120, nullable: true)]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 200, nullable: true)]
    #[Assert\Url]
    private ?string $website = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    public function getCompanyName(): string
    {
        return $this->companyName;
    }

    public function setCompanyName(string $n): self
    {
        $this->companyName = $n;
        return $this;
    }

    public function getVatId(): ?string
    {
        return $this->vatId;
    }

    public function setVatId(?string $v): self
    {
        $this->vatId = $v;
        return $this;
    }

    public function getRegistrationNumber(): ?string
    {
        return $this->registrationNumber;
    }

    public function setRegistrationNumber(?string $n): self
    {
        $this->registrationNumber = $n;
        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $s): self
    {
        $this->street = $s;
        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $p): self
    {
        $this->postalCode = $p;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $c): self
    {
        $this->city = $c;
        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $c): self
    {
        $this->country = $c;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): void
    {
        $this->website = $website;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): void
    {
        $this->notes = $notes;
    }

    public function __toString(): string
    {
        return $this->companyName;
    }

}
