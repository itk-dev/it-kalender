<?php

namespace App\ICS;

use App\Entity\Calendar;
use App\Entity\Person;
use Doctrine\ORM\EntityManagerInterface;
use ICal\Event as IcalEvent;
use ICal\ICal;

final class ICSHelper
{
    private const DATE_FORMAT = 'Y-m-d';
    private const TIME_FORMAT = 'H:i';

    public const MICROSOFT_TRUE = 'TRUE';
    public const MICROSOFT_FALSE = 'FALSE';
    public const MICROSOFT_ALLDAYEVENT = 'X-MICROSOFT-CDO-ALLDAYEVENT';

    public const MICROSOFT_BUSYSTATUS = 'X-MICROSOFT-CDO-BUSYSTATUS';

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function readICS(Person $person): bool
    {
        $ics = file_get_contents($person->getIcsUrl());
        if (0 !== $this->compareICS($ics, $person->getIcs())) {
            $person
                ->setIcs($ics)
                ->setIcsReadAt(new \DateTimeImmutable());
            $this->entityManager->persist($person);
            $this->entityManager->flush();

            return true;
        }

        return false;
    }

    private function compareICS(string $ics0, ?string $ics1): int
    {
        // Remove DTSTAMP (cf. https://www.kanzaki.com/docs/ical/dtstamp.html)
        [$ics0, $ics1] = preg_replace('/^DTSTAMP:.+$/mi', '', [$ics0, $ics1]);

        return $ics0 <=> $ics1;
    }

    public function getCalendarData(Calendar $calendar, \DateTimeImmutable $now = new \DateTimeImmutable(), int $days = 5): array
    {
        $dates = [];

        $startDate = null;
        $endDate = null;
        $index = 0;

        $days = min($days, 14);
        while (count($dates) < $days) {
            $date = new \DateTimeImmutable(sprintf('%s midnight + %d days', $now->format(\DateTimeImmutable::ATOM), $index));

            // Skip Saturdays and Sundays
            // https://www.php.net/manual/en/datetime.format.php#refsect1-datetime.format-parameters
            if ($date->format('N') < 6) {
                if (null === $startDate || $date < $startDate) {
                    $startDate = $date;
                }
                if (null === $endDate || $date > $endDate) {
                    $endDate = $date;
                }
                $dates[] = $date->format(self::DATE_FORMAT);
            }
            ++$index;
        }

        $data = [
            'dates' => $dates,
            'people' => [],
        ];

        foreach ($calendar->getPeople() as $person) {
            $personData = [];
            $ics = $person->getIcs();
            if (!empty($ics)) {
                $events = $this->getEvents($ics, $startDate, $endDate, minDuration: 2 * 60 * 60);
                foreach ($events as $event) {
                    $date = $event->getStartTime()->format(self::DATE_FORMAT);
                    if (in_array($date, $dates)) {
                        [$start, $end] = $this->roundTimes($event);
                        $personData[$date][] = [
                            'summary' => $event->getSummary(),
                            'type' => $event->getBusyStatus()->value,
                            'start' => $start->format(self::TIME_FORMAT),
                            'end' => $end->format(self::TIME_FORMAT),
                            'event' => $event,
                        ];
                    }
                }
            }

            $data['people'][$person->getName()] = $personData;
        }

        return $data;
    }

    public function roundTimes(Event $event): array
    {
        $getHours = static fn (\DateTimeInterface $time) => (int) $time->format('H');
        $setHours = static fn (\DateTimeInterface $time, int $hours) => \DateTimeImmutable::createFromInterface($time)->setTime($hours, 0);

        $stuff = 4;

        $roundHours = static function (\DateTimeInterface $time) use ($setHours, $stuff) {
            $hours = (int) $time->format('H');
            // Hours must not be less than 8.
            $hours = max($hours, 8);
            // Hours must not be more than 16.
            $hours = min($hours, 16);
            // Round to nearest multiple of 4 (cf. https://stackoverflow.com/a/4133886)
            $hours = (int) (round($hours / $stuff) * $stuff);

            return $setHours($time, $hours);
        };

        $start = $event->getStartTime();
        if ($getHours($start) < 8) {
            $start = $setHours($start, 8);
        }

        $end = $event->getEndTime();
        if ($getHours($end) > 16 || $getHours($end) < $getHours($start)) {
            $end = $setHours($end, 16);
        }

        // An event crossing noon is a full day event.
        if ($getHours($start) < 12 && $getHours($end) > 12) {
            $start = $setHours($start, 8);
            $end = $setHours($end, 16);
        }

        [$start, $end] = [$roundHours($start), $roundHours($end)];

        if ($getHours($start) >= $getHours($end)) {
            if ($getHours($start) > 12) {
                $start = $setHours($start, $getHours($end) - $stuff);
            } else {
                $end = $setHours($end, $getHours($start) + $stuff);
            }
        }

        return [$start, $end];
    }

    /**
     * @return array|Event[]
     */
    public function getEvents(string $ics, \DateTimeInterface $start, \DateTimeInterface $end,
        array $busyStatuses = [BusyStatus::OutOfOffice, BusyStatus::WorkingElsewhere],
        int $minDuration = 0
    ): array {
        $now = (new \DateTimeImmutable())->setTime(0, 0, 0);
        $startDiff = $start->diff($now);
        $endDiff = $now->diff($end);
        // https://github.com/u01jmg3/ics-parser#ical-api
        $icalOptions = [
            // Look 4 weeks into the past …
            'filterDaysBefore' => max(0, ($startDiff->days + 4 * 7) * ($startDiff->invert ? -1 : 1)),
            // … and 2 weeks into the future.
            'filterDaysAfter' => max(0, ($endDiff->days + 2 * 7) * ($endDiff->invert ? -1 : 1)),
        ];
        $ical = new ICal($ics, $icalOptions);
        $ical = new ICal($ics, $icalOptions + [
                'defaultTimeZone' => $ical->calendarTimeZone(),
        ]);

        return array_values(
            array_filter(
                array_merge(
                    ...array_map(
                        $this->createEvents(...),
                        $ical->eventsFromRange(
                            $start->format(\DateTime::ATOM),
                            $end->format(\DateTime::ATOM)
                        )
                    )
                ),
                fn (Event $event) => in_array($event->getBusyStatus(), $busyStatuses)
                    && $event->getDuration() > $minDuration
            )
        );
    }

    /**
     * @return Event[]
     */
    private function createEvents(IcalEvent $icalEvent): array
    {
        $event = new Event($icalEvent);
        // Split into days
        $start = $event->getStartTime();
        $end = $event->getEndTime();
        $ranges = [];
        while ($start->format(self::DATE_FORMAT) < $end->format(self::DATE_FORMAT)) {
            $nextDay = new \DateTimeImmutable(sprintf('%s tomorrow', $start->format(self::DATE_FORMAT)));
            $ranges[] = [$start, $nextDay];
            $start = $nextDay;
        }
        if ($start < $end) {
            $ranges[] = [$start, $end];
        }

        return array_map(
            static fn (array $range) => (clone $event)
                ->setStartTime($range[0])
                ->setEndTime($range[1]),
            $ranges
        );
    }

    public function isAllDayEvent(IcalEvent $event): bool
    {
        return self::MICROSOFT_TRUE === $event->x_microsoft_cdo_alldayevent;
    }

    public function getBusyStatus(IcalEvent $event): BusyStatus
    {
        $status = $event->x_microsoft_cdo_busystatus;

        return BusyStatus::from($status);
    }
}
