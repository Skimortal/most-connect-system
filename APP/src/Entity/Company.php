<?php

namespace App\Entity;

use App\Enum\CompanyType;
use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, enumType: CompanyType::class)]
    private CompanyType $type;

    #[ORM\Column(type: 'json')]
    private array $categories = [];

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $uid = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Url]
    private ?string $website = null;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: Address::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $addresses;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $logo = null; // Pfad zur Datei, z.B. /uploads/logo.png

    public function __construct()
    {
        $this->addresses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getType(): CompanyType
    {
        return $this->type;
    }

    public function setType(CompanyType $type): void
    {
        $this->type = $type;
    }

    public function getCategories(): array
    {
        return $this->categories;
    }

    public function setCategories(array $categories): void
    {
        $this->categories = $categories;
    }

    public function getUid(): ?string
    {
        return $this->uid;
    }

    public function setUid(?string $uid): void
    {
        $this->uid = $uid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): void
    {
        $this->website = $website;
    }

    public function getAddresses(): Collection
    {
        return $this->addresses;
    }

    public function setAddresses(Collection $addresses): void
    {
        $this->addresses = $addresses;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): void
    {
        $this->logo = $logo;
    }

    public function __toString(): string
    {
        return $this->name;
    }

}
