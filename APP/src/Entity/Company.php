<?php

namespace App\Entity;

use AllowDynamicProperties;
use App\Enum\CompanyCategoryType;
use App\Enum\CompanyType;
use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;

#[AllowDynamicProperties] #[ORM\Entity(repositoryClass: CompanyRepository::class)]
#[Vich\Uploadable]
#[ORM\HasLifecycleCallbacks]
class Company extends Base
{

    #[ORM\Column(length: 255, enumType: CompanyType::class)]
    private CompanyType $type;

    #[ORM\Column(type: 'json', enumType: CompanyCategoryType::class)]
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

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: User::class)]
    private Collection $users;

    #[Vich\UploadableField(mapping: 'company_logo', fileNameProperty: 'logoName')]
    private ?File $logoFile = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $logoName = null;

    #[Vich\UploadableField(mapping: 'company_logo_small', fileNameProperty: 'logoSmallName')]
    private ?File $logoSmallFile = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $logoSmallName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $bancAccountInstitute = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $bancAccountOwner = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $bancAccountIBAN = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $bancAccountBIC = null;

    public function __construct()
    {
        $this->addresses = new ArrayCollection();
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

    public function addAddress(Address $address): static
    {
        if (!$this->addresses->contains($address)) {
            $this->addresses->add($address);
            $address->setCompany($this);

            if ($this->addresses->count() === 1) {
                $address->setIsMain(true);
            }
        }

        return $this;
    }

    public function removeAddress(Address $address): static
    {
        if ($this->addresses->removeElement($address)) {
            // set the owning side to null (unless already changed)
            if ($address->getCompany() === $this) {
                $address->setCompany(null);
            }
        }

        return $this;
    }

    public function setMainAddress(Address $mainAddress): void
    {
        foreach ($this->getAddresses() as $address) {
            $address->setIsMain($address === $mainAddress);
        }
    }

    public function getMainAddress(): Address
    {
        /** @var Address $address */
        foreach ($this->getAddresses() as $address) {
            if($address->isMain()) {
                return $address;
            }
        }
        return $this->addresses->first();
    }

    public function getLogoFile(): ?File
    {
        return $this->logoFile;
    }

    public function setLogoFile(?File $logoFile): void
    {
        $this->logoFile = $logoFile;
        if (null !== $logoFile) {
            $this->touch();
        }
    }

    public function getLogoName(): ?string
    {
        return $this->logoName;
    }

    public function setLogoName(?string $logoName): void
    {
        $this->logoName = $logoName;
    }

    public function getLogoSmallFile(): ?File
    {
        return $this->logoSmallFile;
    }

    public function setLogoSmallFile(?File $logoSmallFile): void
    {
        $this->logoSmallFile = $logoSmallFile;
        if (null !== $logoSmallFile) {
            $this->touch();
        }
    }

    public function getLogoSmallName(): ?string
    {
        return $this->logoSmallName;
    }

    public function setLogoSmallName(?string $logoSmallName): void
    {
        $this->logoSmallName = $logoSmallName;
    }

    public function getBancAccountInstitute(): ?string
    {
        return $this->bancAccountInstitute;
    }

    public function setBancAccountInstitute(?string $bancAccountInstitute): void
    {
        $this->bancAccountInstitute = $bancAccountInstitute;
    }

    public function getBancAccountOwner(): ?string
    {
        return $this->bancAccountOwner;
    }

    public function setBancAccountOwner(?string $bancAccountOwner): void
    {
        $this->bancAccountOwner = $bancAccountOwner;
    }

    public function getBancAccountIBAN(): ?string
    {
        return $this->bancAccountIBAN;
    }

    public function setBancAccountIBAN(?string $bancAccountIBAN): void
    {
        $this->bancAccountIBAN = $bancAccountIBAN;
    }

    public function getBancAccountBIC(): ?string
    {
        return $this->bancAccountBIC;
    }

    public function setBancAccountBIC(?string $bancAccountBIC): void
    {
        $this->bancAccountBIC = $bancAccountBIC;
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function setUsers(Collection $users): void
    {
        $this->users = $users;
    }

    public function __toString(): string
    {
        return $this->name;
    }

}
