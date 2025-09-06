<?php

namespace App\Entity;

use App\Repository\TarifRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TarifRepository::class)]
#[ORM\Table(name: 'tarif')]
class Tarif extends Base
{
    #[ORM\Column(length: 150)]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(type: 'date_immutable')]
    #[Assert\NotNull]
    private \DateTimeImmutable $validFrom;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?\DateTimeImmutable $validTo = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $furtherInfo = null;

    #[ORM\Column(name: 'aktiv', type: 'boolean')]
    private bool $active = true;

    #[ORM\OneToMany(mappedBy: 'tarif', targetEntity: TarifPosition::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $positions;

    public function __construct()
    {
        $this->positions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getValidFrom(): \DateTimeImmutable
    {
        return $this->validFrom;
    }

    public function setValidFrom(\DateTimeImmutable $d): self
    {
        $this->validFrom = $d;
        return $this;
    }

    public function getValidTo(): ?\DateTimeImmutable
    {
        return $this->validTo;
    }

    public function setValidTo(?\DateTimeImmutable $d): self
    {
        $this->validTo = $d;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $t): self
    {
        $this->description = $t;
        return $this;
    }

    public function getFurtherInfo(): ?string
    {
        return $this->furtherInfo;
    }

    public function setFurtherInfo(?string $t): self
    {
        $this->furtherInfo = $t;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $flag): self
    {
        $this->active = $flag;
        return $this;
    }

    /** @return Collection<int, TarifPosition> */
    public function getPositions(): Collection
    {
        return $this->positions;
    }

    public function addPosition(TarifPosition $p): self
    {
        if (!$this->positions->contains($p)) {
            $this->positions->add($p);
            $p->setTarif($this);
        }
        return $this;
    }

    public function removePosition(TarifPosition $p): self
    {
        if ($this->positions->removeElement($p)) {
            if ($p->getTarif() === $this) {
                $p->setTarif(null);
            }
        }
        return $this;
    }

    public function __toString(): string
    {
        return sprintf('%s (%s%s)', $this->name, $this->validFrom->format('Y-m-d'), $this->validTo?->format(' – Y-m-d') ?: '');
    }
}
