<?php

namespace App\DataFixtures;

use App\Entity\Calendar;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CalendarFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 4; ++$i) {
            $calendar = new Calendar();
            $calendar
                ->setName(sprintf('Calendar %d', $i));
            $this->setReference(sprintf('calendar:calendar%02d', $i), $calendar);
            $manager->persist($calendar);
        }

        $manager->flush();
    }
}
