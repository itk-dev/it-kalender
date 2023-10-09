<?php

namespace App\Tests\ICS;

use App\ICS\BusyStatus;
use App\ICS\ICSHelper;
use Sabre\VObject;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;
use Spatie\IcalendarGenerator\Properties\TextProperty;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ICSHelperTest2 extends WebTestCase
{
    private ICSHelper $icsHelper;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->icsHelper = static::getContainer()->get(ICSHelper::class);
    }

    public function testHmm(): void
    {
        $ics = file_get_contents(__DIR__.'/ics/before-noon-repeating-2-away.ics');
        /** @var VObject\Component\VCalendar $vcalendar */
        $vcalendar = VObject\Reader::read($ics);
        $vcalendar = $vcalendar->expand(new \DateTimeImmutable('2001-01-01'), new \DateTimeImmutable('2100-01-01'));
        foreach ($vcalendar->VEVENT as $event) {
            $this->assertEquals(0, 0);
            foreach ($event->RRULE as $recur) {
                //                $this->assertEquals(0, 0);
            }
        }
    }

    public function testBeforeNoonAway(): void
    {
        $ics = file_get_contents(__DIR__.'/ics/before-noon-away.ics');

        $events = $this->icsHelper->getEvents($ics, new \DateTimeImmutable('2001-01-01'), new \DateTimeImmutable('2100-01-01'),
            minDuration: 2 * 60 * 60
        );

        $this->assertIsArray($events);
        $this->assertCount(1, $events);
        $this->assertEquals(new \DateTimeImmutable('2023-08-30 09:00'), $events[0]->getStartTime());
        $this->assertEquals(new \DateTimeImmutable('2023-08-30 12:00'), $events[0]->getEndTime());
    }

    public function testBeforeNoonRepeating2Away(): void
    {
        $ics = file_get_contents(__DIR__.'/ics/before-noon-repeating-2-away.ics');

        $events = $this->icsHelper->getEvents($ics, new \DateTimeImmutable('2001-01-01'), new \DateTimeImmutable('2100-01-01'),
            minDuration: 2 * 60 * 60
        );

        $this->assertIsArray($events);
        $this->assertCount(2, $events);
        $this->assertEquals(new \DateTimeImmutable('2023-08-30 09:00'), $events[0]->getStartTime());
        $this->assertEquals(new \DateTimeImmutable('2023-08-30 12:00'), $events[0]->getEndTime());
    }

    public function testAfterNoonAway(): void
    {
        $ics = file_get_contents(__DIR__.'/ics/after-noon-away.ics');

        $events = $this->icsHelper->getEvents($ics, new \DateTimeImmutable('2001-01-01'), new \DateTimeImmutable('2100-01-01'),
            minDuration: 2 * 60 * 60
        );

        $this->assertIsArray($events);
        $this->assertCount(1, $events);
        $this->assertEquals(new \DateTimeImmutable('2023-08-30 12:00'), $events[0]->getStartTime());
        $this->assertEquals(new \DateTimeImmutable('2023-08-30 16:00'), $events[0]->getEndTime());
    }

    public function testAcrossNoonAway(): void
    {
        $ics = file_get_contents(__DIR__.'/ics/across-noon-away.ics');

        $events = $this->icsHelper->getEvents($ics, new \DateTimeImmutable('2001-01-01'), new \DateTimeImmutable('2100-01-01'),
            minDuration: 2 * 60 * 60
        );

        $this->assertIsArray($events);
        $this->assertCount(1, $events);
        $this->assertEquals(new \DateTimeImmutable('2023-08-30 11:00'), $events[0]->getStartTime());
        $this->assertEquals(new \DateTimeImmutable('2023-08-30 14:00'), $events[0]->getEndTime());
    }

    public function testAllDayOneDayAway(): void
    {
        $ics = file_get_contents(__DIR__.'/ics/all-day-one-day-away.ics');

        $events = $this->icsHelper->getEvents($ics, new \DateTimeImmutable('2001-01-01'), new \DateTimeImmutable('2100-01-01'),
            minDuration: 2 * 60 * 60
        );

        $this->assertIsArray($events);
        $this->assertCount(1, $events);
        $this->assertEquals(new \DateTimeImmutable('2023-08-30'), $events[0]->getStartTime());
        $this->assertEquals(new \DateTimeImmutable('2023-08-31'), $events[0]->getEndTime());
    }

    public function testAllDayTwoDaysAway(): void
    {
        $ics = file_get_contents(__DIR__.'/ics/all-day-two-days-away.ics');

        $events = $this->icsHelper->getEvents($ics, new \DateTimeImmutable('2001-01-01'), new \DateTimeImmutable('2100-01-01'),
            minDuration: 2 * 60 * 60
        );

        $this->assertIsArray($events);
        $this->assertCount(2, $events);
        $this->assertEquals(new \DateTimeImmutable('2023-08-30'), $events[0]->getStartTime());
        $this->assertEquals(new \DateTimeImmutable('2023-08-31'), $events[0]->getEndTime());
    }

    public function testAllDayRepeating2Away(): void
    {
        $ics = file_get_contents(__DIR__.'/ics/all-day-repeating-2-away.ics');

        $events = $this->icsHelper->getEvents($ics, new \DateTimeImmutable('2001-01-01'), new \DateTimeImmutable('2100-01-01'),
            minDuration: 2 * 60 * 60
        );

        $this->assertIsArray($events);
        $this->assertCount(2, $events);
        $this->assertEquals(new \DateTimeImmutable('2023-08-30'), $events[0]->getStartTime());
        $this->assertEquals(new \DateTimeImmutable('2023-08-31'), $events[0]->getEndTime());
    }

    public function testAllDayRepeating3Away(): void
    {
        $ics = file_get_contents(__DIR__.'/ics/all-day-repeating-3-away.ics');

        $events = $this->icsHelper->getEvents($ics, new \DateTimeImmutable('2001-01-01'), new \DateTimeImmutable('2100-01-01'),
            minDuration: 2 * 60 * 60
        );

        $this->assertIsArray($events);
        $this->assertCount(3, $events);
        $this->assertEquals(new \DateTimeImmutable('2023-08-30'), $events[0]->getStartTime());
        $this->assertEquals(new \DateTimeImmutable('2023-08-31'), $events[0]->getEndTime());
    }

    public function _testGetDataAllDay(): void
    {
        $ics = Calendar::create('Test calendar')
            ->event(Event::create('All day away')
                ->startsAt(new \DateTimeImmutable('2001-01-01'))
                ->endsAt(new \DateTimeImmutable('2001-01-02'))
                ->appendProperty(TextProperty::create(ICSHelper::MICROSOFT_BUSYSTATUS, BusyStatus::OutOfOffice->value))
                ->appendProperty(TextProperty::create(ICSHelper::MICROSOFT_ALLDAYEVENT, ICSHelper::MICROSOFT_TRUE))
            )
            ->get();

        $events = $this->icsHelper->getEvents($ics, new \DateTimeImmutable('2001-01-01'), new \DateTimeImmutable('2002-01-01'),
            minDuration: 2 * 60 * 60
        );
        $this->assertIsArray($events);
        $this->assertCount(1, $events);
    }

    public function _testGetDataAllDayTwoDays(): void
    {
        $ics = Calendar::create('Test calendar')
            ->event(Event::create('All day away')
                ->startsAt(new \DateTimeImmutable('2001-01-01'))
                ->endsAt(new \DateTimeImmutable('2001-01-03'))
                ->appendProperty(TextProperty::create(ICSHelper::MICROSOFT_BUSYSTATUS, BusyStatus::OutOfOffice->value))
                ->appendProperty(TextProperty::create(ICSHelper::MICROSOFT_ALLDAYEVENT, ICSHelper::MICROSOFT_TRUE))
            )
            ->get();

        $events = $this->icsHelper->getEvents($ics, new \DateTimeImmutable('2001-01-01'), new \DateTimeImmutable('2002-01-01'),
            minDuration: 2 * 60 * 60
        );
        $this->assertIsArray($events);
        $this->assertCount(1, $events);
    }

    public function _testGetDataAllDayRepeating(): void
    {
        $ics = Calendar::create('Test calendar')
            ->event(Event::create('All day away repeating on two consecutive days')
                ->startsAt(new \DateTimeImmutable('2001-01-01'))
                ->endsAt(new \DateTimeImmutable('2001-01-02'))
                ->appendProperty(TextProperty::create(ICSHelper::MICROSOFT_BUSYSTATUS, BusyStatus::OutOfOffice->value))
                ->appendProperty(TextProperty::create(ICSHelper::MICROSOFT_ALLDAYEVENT, ICSHelper::MICROSOFT_TRUE))
                ->repeatOn(new \DateTime('2001-01-02'))
            )
            ->get();

        $events = $this->icsHelper->getEvents($ics, new \DateTimeImmutable('2001-01-01'), new \DateTimeImmutable('2002-01-01'),
            minDuration: 2 * 60 * 60
        );
        $this->assertIsArray($events);
        $this->assertCount(1, $events);
    }
}
