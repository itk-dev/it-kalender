<?php

namespace App\Entity;

use App\Repository\PersonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
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

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $ics = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $icsReadAt = null;

    #[ORM\ManyToMany(targetEntity: Calendar::class, mappedBy: 'people')]
    private Collection $calendars;

    public function __construct()
    {
        $this->calendars = new ArrayCollection();
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

    public function getIcs(): ?string
    {
        return $this->ics;
    }

    public function setIcs(?string $ics): static
    {
        $this->ics = $ics;

        return $this;
    }

    public function getIcsReadAt(): ?\DateTimeImmutable
    {
        return $this->icsReadAt;
    }

    public function setIcsReadAt(?\DateTimeImmutable $icsReadAt): static
    {
        $this->icsReadAt = $icsReadAt;

        return $this;
    }

    /**
     * @return Collection<int, Calendar>
     */
    public function getCalendars(): Collection
    {
        return $this->calendars;
    }

    public function addCalendar(Calendar $calendar): static
    {
        if (!$this->calendars->contains($calendar)) {
            $this->calendars->add($calendar);
            $calendar->addPerson($this);
        }

        return $this;
    }

    public function removeCalendar(Calendar $calendar): static
    {
        if ($this->calendars->removeElement($calendar)) {
            $calendar->removePerson($this);
        }

        return $this;
    }
}
