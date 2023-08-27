<?php

namespace App\ICS;

use PHPUnit\Framework\TestCase;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;
use Spatie\IcalendarGenerator\Properties\TextProperty;

class ICSHelperTest extends TestCase
{
    public function testGetData(): void
    {
        $ics = Calendar::create('Test calendar')
            ->event(Event::create('Test event')
                ->startsAt(new \DateTimeImmutable('2001-01-01 09:00:00'))
                ->endsAt(new \DateTimeImmutable('2001-01-01 12:00:00'))
                ->appendProperty(TextProperty::create(ICSHelper::MICROSOFT_BUSYSTATUS, BusyStatus::WorkingElsewhere->value))
            )
            ->event(Event::create('Test event')
                ->startsAt(new \DateTimeImmutable('2001-01-01 09:00:00'))
                ->appendProperty(TextProperty::create(ICSHelper::MICROSOFT_ALLDAYEVENT, ICSHelper::MICROSOFT_TRUE))
                ->fullDay()
            )
            ->get();

        $name = 'test';

        $helper = new ICSHelper([
            'ics_urls' => [
                $name => $ics,
            ],
        ]);

        $events = $helper->getEvents(
            new \DateTimeImmutable('2001-01-01'),
            new \DateTimeImmutable('2001-12-31'),
            minDuration: 2 * 60 * 60
        );

        $this->assertArrayHasKey($name, $events);
        $this->assertCount(1, $events[$name]);
    }
}
