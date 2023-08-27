<?php

namespace App\ICS;

use ICal\Event as IcalEvent;

class Event
{
    private const MICROSOFT_TRUE = 'TRUE';

    public function __construct(
        private readonly IcalEvent $event
    ) {
    }

    public function __get(string $name)
    {
        return $this->event->{$name};
    }

    public function getStartTime(): \DateTimeInterface
    {
        return new \DateTimeImmutable($this->event->dtstart);
    }

    public function getEndTime(): \DateTimeInterface
    {
        return $this->event->dtend
            ? new \DateTimeImmutable($this->event->dtend)
            : new \DateTimeImmutable($this->getStartTime()->format(\DateTimeImmutable::ATOM).' tomorrow');
    }

    public function getBusyStatus(): BusyStatus
    {
        return BusyStatus::from($this->event->x_microsoft_cdo_busystatus ?? BusyStatus::FREE->value);
    }

    /**
     * Get duration in seconds.
     */
    public function getDuration(): int
    {
        return $this->getEndTime()->getTimestamp() - $this->getStartTime()->getTimestamp();
    }
}
