<?php

namespace App\ICS;

use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;
use Spatie\IcalendarGenerator\Properties\TextProperty;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ICSHelperTest extends WebTestCase
{
    private ICSHelper $icsHelper;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->icsHelper = static::getContainer()->get(ICSHelper::class);
    }

    public function testMultiDayEventsAllDay(): void
    {
        $ics = Calendar::create('Test calendar')
            ->event(Event::create('All day event two days')
                ->startsAt(new \DateTimeImmutable('2001-01-01'))
                ->endsAt(new \DateTimeImmutable('2001-01-03'))
                ->appendProperty(TextProperty::create(ICSHelper::MICROSOFT_BUSYSTATUS, BusyStatus::WorkingElsewhere->value))
            )
            ->get();

        $events = $this->icsHelper->getEvents(
            $ics,
            new \DateTimeImmutable('2001-01-01'),
            new \DateTimeImmutable('2001-12-31'),
            minDuration: 2 * 60 * 60
        );

        $this->assertCount(2, $events);
        $this->assertEquals(new \DateTimeImmutable('2001-01-01'), $events[0]->getStartTime());
        $this->assertEquals(new \DateTimeImmutable('2001-01-02'), $events[0]->getEndTime());
        $this->assertEquals(new \DateTimeImmutable('2001-01-02'), $events[1]->getStartTime());
        $this->assertEquals(new \DateTimeImmutable('2001-01-03'), $events[1]->getEndTime());
    }

    public function testMultiDayEvents(): void
    {
        $ics = Calendar::create('Test calendar')
            ->event(Event::create('Test event two days')
                ->startsAt(new \DateTimeImmutable('2001-01-01 09:00'))
                ->endsAt(new \DateTimeImmutable('2001-01-03 17:00'))
                ->appendProperty(TextProperty::create(ICSHelper::MICROSOFT_BUSYSTATUS, BusyStatus::WorkingElsewhere->value))
            )
            ->get();

        $events = $this->icsHelper->getEvents(
            $ics,
            new \DateTimeImmutable('2001-01-01'),
            new \DateTimeImmutable('2001-12-31'),
            minDuration: 2 * 60 * 60
        );

        $this->assertCount(3, $events);
        $this->assertEquals(new \DateTimeImmutable('2001-01-01 09:00'), $events[0]->getStartTime());
        $this->assertEquals(new \DateTimeImmutable('2001-01-02 00:00'), $events[0]->getEndTime());
        $this->assertEquals(new \DateTimeImmutable('2001-01-02 00:00'), $events[1]->getStartTime());
        $this->assertEquals(new \DateTimeImmutable('2001-01-03 00:00'), $events[1]->getEndTime());
        $this->assertEquals(new \DateTimeImmutable('2001-01-03 00:00'), $events[2]->getStartTime());
        $this->assertEquals(new \DateTimeImmutable('2001-01-03 17:00'), $events[2]->getEndTime());
    }
}
