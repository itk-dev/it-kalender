<?php

namespace App\DataFixtures;

use App\Entity\CalendarPerson;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CalendarPersonFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $person = new CalendarPerson();
        $person
            ->setCalendar($this->getReference('calendar:test'))
            ->setPerson($this->getReference('person:person-0'))
            ->setPosition(1);
        $manager->persist($person);

        $person = new CalendarPerson();
        $person
            ->setCalendar($this->getReference('calendar:test'))
            ->setPerson($this->getReference('person:person-1'))
            ->setPosition(0);
        $manager->persist($person);

        $person = new CalendarPerson();
        $person
            ->setCalendar($this->getReference('calendar:test'))
            ->setPerson($this->getReference('person:person-2'))
            ->setPosition(2);
        $manager->persist($person);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CalendarFixture::class,
            PersonFixture::class,
        ];
    }
}
