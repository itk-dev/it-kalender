<?php

namespace App\Entity;

use App\Repository\PersonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: PersonRepository::class)]
class Person
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $icsUrl = null;

    #[ORM\OneToMany(mappedBy: 'person', targetEntity: CalendarPerson::class)]
    private Collection $calendarPeople;

    public function __construct()
    {
        $this->calendarPeople = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getName() ?? self::class;
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getIcsUrl(): ?string
    {
        return $this->icsUrl;
    }

    public function setIcsUrl(string $icsUrl): static
    {
        $this->icsUrl = $icsUrl;

        return $this;
    }

    public function getCalendars(): array
    {
        return array_map(
            static fn (CalendarPerson $calendarPerson) => $calendarPerson->getCalendar(),
            $this->calendarPeople->toArray()
        );
    }
}
