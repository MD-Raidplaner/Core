<?php

namespace rp\event\event;

use wcf\event\IPsr14Event;

/**
 * Indicates that multiple events have been deleted.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @property-read Event[] $events
 */
final class EventsDeleted implements IPsr14Event
{
    public function __construct(
        public readonly array $events,
    ) {}
}
