<?php

namespace rp\system\calendar;

use rp\data\event\ViewableEvent;
use wcf\util\DateUtil;

/**
 * Represents a day in the calendar and manages the associated events.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class Day
{
    private \DateTimeImmutable $dayObj;

    /**
     * @var DayEvent[]
     */
    private array $events = [];

    public function __construct(
        private readonly int $year,
        private readonly int $month,
        private readonly int $day
    ) {
        $this->dayObj = new \DateTimeImmutable("{$year}-{$month}-{$day}");
    }

    /**
     * Returns the date of the day as a string in the format 'Y-m-d'.
     */
    public function __toString(): string
    {
        return $this->dayObj->format('Y-m-d');
    }

    /**
     * Adds an event for this day.
     */
    public function addEvent(DayEvent $dayEvent): void
    {
        $this->events[] = $dayEvent;
    }

    /**
     * Returns the day.
     */
    public function getDay(): int
    {
        return $this->day;
    }

    /**
     * Returns events for this day.
     */
    public function getEvents(): array
    {
        return $this->events;
    }
}
