<?php

namespace App\DataFixtures;

use App\Entity\Calendar;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CalendarFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $calendar = new Calendar();
        $calendar
            ->setName('Test calendar')
            ->setSlug('test');
        $this->setReference('calendar:test', $calendar);
        $manager->persist($calendar);

        $manager->flush();
    }
}
