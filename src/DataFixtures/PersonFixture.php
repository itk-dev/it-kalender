<?php

namespace App\DataFixtures;

use App\Entity\Person;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PersonFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 10; ++$i) {
            $person = new Person();
            $person
                ->setName(sprintf('Person %d', $i))
                ->setIcsUrl(sprintf('https://example.com/person%d.ics', $i));
            $this->setReference(sprintf('person:person%02d', $i), $person);
            $manager->persist($person);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CalendarFixture::class,
        ];
    }
}
