<?php

namespace App\ICS;

use ICal\Event as IcalEvent;
use ICal\ICal;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ICSHelper
{
    private $options;

    public const MICROSOFT_TRUE = 'TRUE';
    public const MICROSOFT_FALSE = 'FALSE';
    public const MICROSOFT_ALLDAYEVENT = 'X-MICROSOFT-CDO-ALLDAYEVENT';

    public const MICROSOFT_BUSYSTATUS = 'X-MICROSOFT-CDO-BUSYSTATUS';

    public function __construct(array $options)
    {
        $this->options = $this->resolveOptions($options);
    }

    /**
     * @return array|Event[]
     */
    public function getEvents(\DateTimeInterface $start, \DateTimeInterface $end,
        array $busyStatuses = [BusyStatus::OutOfOffice, BusyStatus::WorkingElsewhere],
        int $minDuration = 0
    ): array {
        $events = [];
        foreach ($this->options['ics_urls'] as $name => $url) {
            // https://github.com/u01jmg3/ics-parser#are-you-using-outlook
            $ical = new ICal($url);
            $events[$name] = array_values(
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

        return $events;
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

    private function resolveOptions(array $options): array
    {
        return (new OptionsResolver())
          ->setRequired('ics_urls')
          ->resolve($options);
    }
}
