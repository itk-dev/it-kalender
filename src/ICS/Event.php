<?php

namespace App\ICS;

use ICal\Event as IcalEvent;

class Event
{
    private ?\DateTimeImmutable $startTime = null;
    private ?\DateTimeImmutable $endTime = null;

    public function __construct(
        private readonly IcalEvent $event
    ) {
    }

    public function __get(string $name): mixed
    {
        return $this->event->{$name};
    }

    public function getSummary(): string
    {
        return $this->event->summary;
    }

    public function getStartTime(): \DateTimeImmutable
    {
        return $this->startTime ?? new \DateTimeImmutable($this->event->dtstart);
    }

    public function setStartTime(?\DateTimeImmutable $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): \DateTimeImmutable
    {
        return $this->endTime ?? ($this->event->dtend
            ? new \DateTimeImmutable($this->event->dtend)
            : new \DateTimeImmutable($this->getStartTime()->format(\DateTimeImmutable::ATOM).' tomorrow'));
    }

    public function setEndTime(?\DateTimeImmutable $endTime): self
    {
        $this->endTime = $endTime;

        return $this;
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
