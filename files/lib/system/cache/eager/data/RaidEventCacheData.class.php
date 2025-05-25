<?php

namespace rp\system\cache\eager\data;

use rp\data\raid\event\RaidEvent;

/**
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class RaidEventCacheData
{
    public function __construct(
        /** @var array<int, RaidEvent> */
        public readonly array $raidEvents,
    ) {}

    /**
     * Returns the raid event with the given raid event id or 
     * `null` if no such raid event exists.
     */
    public function getEvent(int $eventID): ?RaidEvent
    {
        return $this->raidEvents[$eventID] ?? null;
    }

    /**
     * Returns all raid events.
     * 
     * @return array<int, RaidEvent>
     */
    public function getEvents(): array
    {
        return $this->raidEvents;
    }

    /**
     * Returns the raid events with the given raid event ids.
     * 
     * @param array<int> $eventIDs
     * @return array<int, RaidEvent>
     */
    public function getEventsByIDs(array $eventIDs): array
    {
        return \array_filter(
            \array_map(fn ($eventID) => $this->getEventByID($eventID), $eventIDs),
            fn ($event) => $event !== null
        );
    }
}
