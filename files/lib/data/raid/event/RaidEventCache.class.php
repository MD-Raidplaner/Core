<?php

namespace rp\data\raid\event;

use rp\system\cache\builder\RaidEventCacheBuilder;
use wcf\system\SingletonFactory;

/**
 * Manages the raid event cache.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class RaidEventCache extends SingletonFactory
{
    /**
     * cached raid events
     * @var RaidEvent[]
     */
    protected array $cachedRaidEvents = [];

    /**
     * Returns the raid event with the given raid event id or 
     * `null` if no such raid event exists.
     */
    public function getEventByID(int $eventID): ?RaidEvent
    {
        return $this->cachedRaidEvents[$eventID] ?? null;
    }

    /**
     * Returns all raid events.
     * 
     * @return	RaidEvent[]
     */
    public function getEvents(): array
    {
        return $this->cachedRaidEvents;
    }

    /**
     * Returns the raid events with the given raid event ids.
     */
    public function getEventsByIDs(array $eventIDs): array
    {
        return \array_filter(
            \array_map(fn ($eventID) => $this->getEventByID($eventID), $eventIDs),
            fn ($event) => $event !== null
        );
    }

    [\Override]
    protected function init(): void
    {
        $this->cachedRaidEvents = RaidEventCacheBuilder::getInstance()->getData(['gameID' => RP_CURRENT_GAME_ID]);
    }
}
