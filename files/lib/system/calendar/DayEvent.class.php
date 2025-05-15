<?php

namespace rp\system\calendar;

use rp\data\event\ViewableEvent;
use wcf\system\exception\SystemException;
use wcf\util\DateUtil;

/**
 * Represents a Day Event which decorates a ViewableEvent object.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class DayEvent
{
    const EVENT_STATUS_NONE = 0;
    const EVENT_STATUS_START = 1;
    const EVENT_STATUS_MIDDLE = 2;
    const EVENT_STATUS_END = 3;

    /**
     * The status of the event.
     * 
     * Value range:
     * - EVENT_STATUS_NONE: One-day event.
     * - EVENT_STATUS_START: Start of an event that lasts more than one day.
     * - EVENT_STATUS_MIDDLE: Middle range between the start and end of an event lasting several days.
     * - EVENT_STATUS_END: End of an event lasting several days.
     * 
     * The value of this field is updated by methods of the event class and should not be changed directly.
     */
    private int $eventStatus = DayEvent::EVENT_STATUS_NONE;

    /**
     * The ViewableEvent object being decorated.
     */
    public function __construct(
        private readonly Day $day,
        private readonly ViewableEvent $event
    ) {
    }

    /**
     * Delegates inaccessible methods calls to the decorated event.
     * 
     * @throws  SystemException
     */
    public function __call(string $name, array $arguments): mixed
    {
        return $this->event->__call($name, $arguments);
    }

    /**
     * Provides access to properties of the decorated event.
     */
    public function __get(string $name): mixed
    {
        return $this->event->__get($name);
    }

    /**
     * Checks if a property of the decorated event is set.
     */
    public function __isset(string $name): bool
    {
        return $this->event->__isset($name);
    }

    /**
     * Returns day of this event.
     */
    public function getDay(): Day
    {
        return $this->day;
    }

    /**
     * Returns the decorated ViewableEvent object.
     */
    public function getEvent(): ViewableEvent
    {
        return $this->event;
    }

    /**
     * Returns the current status of the event.
     */
    public function getStatus(): int
    {
        return $this->eventStatus;
    }

    /**
     * Sets the status of the event.
     */
    public function setStatus(int $status): void
    {
        $this->eventStatus = $status;
    }
}
