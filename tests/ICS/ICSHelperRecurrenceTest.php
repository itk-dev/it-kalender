<?php

namespace App\Tests\ICS;

use App\ICS\ICSHelper;
use ICal\ICal;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ICSHelperRecurrenceTest extends WebTestCase
{
    private ICSHelper $icsHelper;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->icsHelper = static::getContainer()->get(ICSHelper::class);
    }

    public function testFridailyThreeTimes(): void
    {
        $ics = file_get_contents(__DIR__.'/ics/thunderbird-fridaily-three-times.ics');
        $ical = new ICal($ics);
        $this->assertCount(3, $ical->events());
    }

    public function testHmm(): void
    {
        $testIcal = <<<ICS
BEGIN:VCALENDAR
METHOD:PUBLISH
PRODID:Microsoft Exchange Server 2010
VERSION:2.0
X-WR-CALNAME:Tilstedeværelse
BEGIN:VTIMEZONE
TZID:Romance Standard Time
BEGIN:STANDARD
DTSTART:16010101T030000
TZOFFSETFROM:+0200
TZOFFSETTO:+0100
RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=-1SU;BYMONTH=10
END:STANDARD
BEGIN:DAYLIGHT
DTSTART:16010101T020000
TZOFFSETFROM:+0100
TZOFFSETTO:+0200
RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=-1SU;BYMONTH=3
END:DAYLIGHT
END:VTIMEZONE
BEGIN:VEVENT
RRULE:FREQ=WEEKLY;UNTIL=20230919T220000Z;INTERVAL=1;BYDAY=TU,WE;WKST=SU
UID:040000008200E00074C5B7101A82E00800000000C5BA1206E4DBD901000000000000000
 010000000708E11944AB7594FB7007D11E359307F
SUMMARY:Away
DTSTART;VALUE=DATE:20230905
DTEND;VALUE=DATE:20230906
CLASS:PUBLIC
PRIORITY:5
DTSTAMP:20230831T083519Z
TRANSP:OPAQUE
STATUS:CONFIRMED
SEQUENCE:0
X-MICROSOFT-CDO-APPT-SEQUENCE:0
X-MICROSOFT-CDO-BUSYSTATUS:OOF
X-MICROSOFT-CDO-INTENDEDSTATUS:BUSY
X-MICROSOFT-CDO-ALLDAYEVENT:TRUE
X-MICROSOFT-CDO-IMPORTANCE:1
X-MICROSOFT-CDO-INSTTYPE:1
X-MICROSOFT-DONOTFORWARDMEETING:FALSE
X-MICROSOFT-DISALLOW-COUNTER:FALSE
X-MICROSOFT-REQUESTEDATTENDANCEMODE:DEFAULT
END:VEVENT
END:VCALENDAR
ICS;

        $actuals = [];
        foreach ([
                     'UTC',
                     'Europe/Copenhagen',
                 ] as $timezone) {
            $ical = new ICal($testIcal);
            $ical = new ICal(false, [
                'defaultTimeZone' => $ical->calendarTimeZone(),
            ]);
            $ical->initString($testIcal);
            $actuals[$timezone] = $ical->events();
        }

        $this->assertCount(6, $actuals['UTC']);
        $this->assertEquals(
            count($actuals['UTC']),
            count($actuals['Europe/Copenhagen']),
        );

        $events = $this->icsHelper->getEvents($testIcal,
            new \DateTimeImmutable('2001-01-01'),
            new \DateTimeImmutable('2101-01-01'),
        );

        $this->assertCount(6, $events);
    }
}
