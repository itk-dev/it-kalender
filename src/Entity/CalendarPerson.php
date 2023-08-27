<?php

namespace App\Entity;

use App\Repository\CalendarPersonRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

#[ORM\Entity(repositoryClass: CalendarPersonRepository::class)]
class CalendarPerson
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'calendarPeople')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Calendar $calendar = null;

    #[ORM\ManyToOne(inversedBy: 'calendarPeople')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Person $person = null;

    #[ORM\Column]
    #[PositiveOrZero]
    private int $position = 0;

    public function __toString(): string
    {
        return $this->person?->getName() ?? self::class;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCalendar(): ?Calendar
    {
        return $this->calendar;
    }

    public function setCalendar(?Calendar $calendar): static
    {
        $this->calendar = $calendar;

        return $this;
    }

    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function setPerson(?Person $person): static
    {
        $this->person = $person;

        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }
}