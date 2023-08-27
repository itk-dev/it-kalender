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
            ->setCalendar($this->getReference('calendar:calendar01'))
            ->setPerson($this->getReference('person:person02'))
            ->setPosition(0);
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
