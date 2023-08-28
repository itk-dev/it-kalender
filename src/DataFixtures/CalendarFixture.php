<?php

namespace App\DataFixtures;

use App\Entity\Calendar;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CalendarFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $calendar = new Calendar();
        $calendar
            ->setName('Test calendar')
            ->setSlug('test')
            ->addPerson($this->getReference('person:person-0'))
            ->addPerson($this->getReference('person:person-1'))
            ->addPerson($this->getReference('person:person-2'))
        ;
        $manager->persist($calendar);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            PersonFixture::class,
        ];
    }
}
