<?php
// src/Entity/Timesheet.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
class Timesheet
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable:false)]
    private User $user;

    #[ORM\Column(type:"date")]
    private \DateTimeInterface $periodStart;

    #[ORM\Column(type:"date")]
    private \DateTimeInterface $periodEnd;

    #[ORM\OneToMany(targetEntity: TimeEntry::class, mappedBy: "user")]
    private Collection $entries;

    #[ORM\Column(type:"integer")]
    private int $totalMinutes = 0;

    #[ORM\Column(type:"string", length:20)]
    private string $status = 'open';

    public function __construct()
    {
        $this->entries = new ArrayCollection();
    }

    public function calculateTotal(): void
    {
        $sum = 0;
        foreach ($this->entries as $e) {
            $sum += $e->getDuration();
        }
        $this->totalMinutes = $sum;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getPeriodStart(): \DateTimeInterface
    {
        return $this->periodStart;
    }

    public function setPeriodStart(\DateTimeInterface $periodStart): void
    {
        $this->periodStart = $periodStart;
    }

    public function getPeriodEnd(): \DateTimeInterface
    {
        return $this->periodEnd;
    }

    public function setPeriodEnd(\DateTimeInterface $periodEnd): void
    {
        $this->periodEnd = $periodEnd;
    }

    public function getEntries(): Collection
    {
        return $this->entries;
    }

    public function setEntries(Collection $entries): void
    {
        $this->entries = $entries;
    }

    public function getTotalMinutes(): int
    {
        return $this->totalMinutes;
    }

    public function setTotalMinutes(int $totalMinutes): void
    {
        $this->totalMinutes = $totalMinutes;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

}
