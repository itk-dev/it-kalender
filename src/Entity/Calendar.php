<?php

namespace App\Entity;

use App\Repository\CalendarRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CalendarRepository::class)]
#[UniqueEntity(fields: ['slug'])]
#[ORM\HasLifecycleCallbacks]
class Calendar
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'calendar', targetEntity: CalendarPerson::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    #[ORM\OrderBy(['position' => Criteria::ASC])]
    #[Assert\Valid]
    private Collection $calendarPeople;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank]
    private ?string $slug = null;

    public function __construct()
    {
        $this->calendarPeople = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->getName() ?? self::class;
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

    /**
     * @return Collection<int, CalendarPerson>
     */
    public function getCalendarPeople(): Collection
    {
        return $this->calendarPeople;
    }

    public function addCalendarPerson(CalendarPerson $calendarPerson): static
    {
        if (!$this->calendarPeople->contains($calendarPerson)) {
            $this->calendarPeople->add($calendarPerson);
            $calendarPerson->setCalendar($this);
        }

        return $this;
    }

    public function removeCalendarPerson(CalendarPerson $calendarPerson): static
    {
        if ($this->calendarPeople->removeElement($calendarPerson)) {
            // set the owning side to null (unless already changed)
            if ($calendarPerson->getCalendar() === $this) {
                $calendarPerson->setCalendar(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setPeoplePositions()
    {
        $position = 0;
        foreach ($this->getCalendarPeople() as $calendarPerson) {
            $calendarPerson->setPosition($position);
            ++$position;
        }
    }
}
