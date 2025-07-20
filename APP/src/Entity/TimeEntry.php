<?php
// src/Entity/TimeEntry.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: App\Repository\TimeEntryRepository::class)]
#[ORM\HasLifecycleCallbacks]
class TimeEntry
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "timeEntries")]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\Column(type:"date")]
    private \DateTimeInterface $date;

    #[ORM\Column(type:"time")]
    private \DateTimeInterface $startTime;

    #[ORM\Column(type:"time")]
    private \DateTimeInterface $endTime;

    #[ORM\Column(type:"integer", options:["default" => 0])]
    private int $breakMinutes = 0;

    #[ORM\Column(type:"integer")]
    private int $duration;

    #[ORM\Column(type:"text", nullable:true)]
    private ?string $notes = null;

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function calculateDuration(): void
    {
        $start = \DateTimeImmutable::createFromMutable($this->startTime);
        $end   = \DateTimeImmutable::createFromMutable($this->endTime);
        $minutes = max(0, ($end->getTimestamp() - $start->getTimestamp()) / 60 - $this->breakMinutes);
        $this->duration = (int) $minutes;
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

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): void
    {
        $this->date = $date;
    }

    public function getStartTime(): \DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): void
    {
        $this->startTime = $startTime;
    }

    public function getEndTime(): \DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeInterface $endTime): void
    {
        $this->endTime = $endTime;
    }

    public function getBreakMinutes(): int
    {
        return $this->breakMinutes;
    }

    public function setBreakMinutes(int $breakMinutes): void
    {
        $this->breakMinutes = $breakMinutes;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): void
    {
        $this->duration = $duration;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): void
    {
        $this->notes = $notes;
    }

}
