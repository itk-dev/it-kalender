<?php

namespace App\DataFixtures;

use App\Entity\Person;
use App\ICS\BusyStatus;
use App\ICS\ICSHelper;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;
use Spatie\IcalendarGenerator\Properties\TextProperty;

class PersonFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $person = new Person();
        $person->setName('Person 0');
        $ics = Calendar::create($person->getName())
            ->event(Event::create('Test event 0')
                ->startsAt(new \DateTimeImmutable('2001-01-01 09:00:00'))
                ->endsAt(new \DateTimeImmutable('2001-01-01 12:00:00'))
                ->appendProperty(TextProperty::create(ICSHelper::MICROSOFT_BUSYSTATUS, BusyStatus::WorkingElsewhere->value))
            )
            ->event(Event::create('Test event 1')
                ->startsAt(new \DateTimeImmutable('2001-01-02 09:00:00'))
                ->appendProperty(TextProperty::create(ICSHelper::MICROSOFT_ALLDAYEVENT, ICSHelper::MICROSOFT_TRUE))
                ->appendProperty(TextProperty::create(ICSHelper::MICROSOFT_BUSYSTATUS, BusyStatus::OutOfOffice->value))
                ->fullDay()
            )
            ->event(Event::create('Test event 2')
                ->startsAt(new \DateTimeImmutable('2001-01-03 08:00:00'))
                ->endsAt(new \DateTimeImmutable('2001-01-03 12:00:00'))
                ->appendProperty(TextProperty::create(ICSHelper::MICROSOFT_BUSYSTATUS, BusyStatus::OutOfOffice->value))
            )
            ->get();
        $filename = tempnam(sys_get_temp_dir(), uniqid());
        file_put_contents($filename, $ics);
        $person->setIcsUrl('file://'.$filename);
        $this->setReference('person:person-0', $person);
        $manager->persist($person);

        $person = new Person();
        $person->setName('Person 1');
        $ics = Calendar::create($person->getName())
            ->event(Event::create('Test event')
                ->startsAt(new \DateTimeImmutable('2001-01-01 12:00:00'))
                ->endsAt(new \DateTimeImmutable('2001-01-01 16:00:00'))
                ->appendProperty(TextProperty::create(ICSHelper::MICROSOFT_BUSYSTATUS, BusyStatus::WorkingElsewhere->value))
            )
            ->event(Event::create('Test event')
                ->startsAt(new \DateTimeImmutable('2001-01-01 09:00:00'))
                ->appendProperty(TextProperty::create(ICSHelper::MICROSOFT_ALLDAYEVENT, ICSHelper::MICROSOFT_TRUE))
                ->fullDay()
            )
            ->get();
        $filename = tempnam(sys_get_temp_dir(), uniqid());
        file_put_contents($filename, $ics);
        $person->setIcsUrl('file://'.$filename);
        $this->setReference('person:person-1', $person);
        $manager->persist($person);

        $person = new Person();
        $person->setName('Person 2');
        $ics = Calendar::create($person->getName())
            ->event(Event::create('Test event spanning noon')
                ->startsAt(new \DateTimeImmutable('2001-01-01 11:00:00'))
                ->endsAt(new \DateTimeImmutable('2001-01-01 14:00:00'))
                ->appendProperty(TextProperty::create(ICSHelper::MICROSOFT_BUSYSTATUS, BusyStatus::WorkingElsewhere->value))
            )
            ->get();
        $filename = tempnam(sys_get_temp_dir(), uniqid());
        file_put_contents($filename, $ics);
        $person->setIcsUrl('file://'.$filename);
        $this->setReference('person:person-2', $person);
        $manager->persist($person);

        $manager->flush();
    }
}
