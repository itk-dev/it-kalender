<?php

namespace App\ICS;

use App\Entity\Calendar;
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

    public function getCalendarData(Calendar $calendar, \DateTimeImmutable $now = new \DateTimeImmutable()): array
    {
        $dates = [];

        $startDate = null;
        $endDate = null;
        $index = 0;
        while (count($dates) < 5) {
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
        $setHours = function (\DateTimeInterface $time, int $hours): \DateTimeInterface {
            return \DateTimeImmutable::createFromInterface($time)->setTime($hours, 0);
        };

        $roundHours = function (\DateTimeInterface $time) use ($setHours) {
            $hours = (int) $time->format('H');
            // Hours must not be less than 8.
            $hours = max($hours, 8);
            // Hours must not be more than 16.
            $hours = min($hours, 16);
            // Round to nearest multiple of 4 (cf. https://stackoverflow.com/a/4133886)
            $stuff = 4;
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

        return [$roundHours($start), $roundHours($end)];
    }

    /**
     * @return array|Event[]
     */
    public function getEvents(string $ics, \DateTimeInterface $start, \DateTimeInterface $end,
        array $busyStatuses = [BusyStatus::OutOfOffice, BusyStatus::WorkingElsewhere],
        int $minDuration = 0
    ): array {
        // https://github.com/u01jmg3/ics-parser#are-you-using-outlook
        $ical = new ICal($ics);

        return array_values(
            array_filter(
                array_map(
                    static fn (IcalEvent $icalEvent) => new Event($icalEvent),
                    $ical->eventsFromRange(
                        $start->format(\DateTime::ATOM),
                        $end->format(\DateTime::ATOM)
                    ),
                ),
                fn (Event $event) => in_array($event->getBusyStatus(), $busyStatuses)
                    && $event->getDuration() > $minDuration
            )
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
