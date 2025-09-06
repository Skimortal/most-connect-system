<?php

namespace App\Entity;

use App\Enum\TarifItemCategory;
use App\Repository\TarifPositionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TarifPositionRepository::class)]
#[ORM\Table(name: 'tarif_position')]
class TarifPosition extends Base
{
    #[ORM\ManyToOne(inversedBy: 'positions')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Tarif $tarif = null;

    #[ORM\Column(enumType: TarifItemCategory::class)]
    private TarifItemCategory $category;

    #[ORM\Column(length: 50)]
    private string $numberLabel; // z.B. "1.2"

    #[ORM\Column(length: 200)]
    private string $name;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 4)]
    #[Assert\PositiveOrZero]
    private string $tarifValue = '0.0000';

    public function __construct(TarifItemCategory $category, string $numberLabel, string $name, float $tarifValue)
    {
        $this->category = $category;
        $this->numberLabel = $numberLabel;
        $this->name = $name;
        $this->setTarifValue($tarifValue);
    }

    public function getTarif(): ?Tarif
    {
        return $this->tarif;
    }

    public function setTarif(?Tarif $t): self
    {
        $this->tarif = $t;
        return $this;
    }

    public function getCategory(): TarifItemCategory
    {
        return $this->category;
    }

    public function setCategory(TarifItemCategory $c): self
    {
        $this->category = $c;
        return $this;
    }

    public function getNumberLabel(): string
    {
        return $this->numberLabel;
    }

    public function setNumberLabel(string $s): self
    {
        $this->numberLabel = $s;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $s): self
    {
        $this->name = $s;
        return $this;
    }

    public function getTarifValue(): float
    {
        return (float)$this->tarifValue;
    }

    public function setTarifValue(float $v): self
    {
        $this->tarifValue = number_format($v, 4, '.', '');
        return $this;
    }

    public function __toString(): string
    {
        return sprintf('[%s] %s – %s (%.4f)', $this->category->value, $this->numberLabel, $this->name, $this->getTarifValue());
    }
}
