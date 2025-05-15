<?php

namespace rp\event\event;

use rp\data\event\Event;
use wcf\event\IPsr14Event;

/**
 * Indicates that a event has been trashed.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class EventTrashed implements IPsr14Event
{
    public function __construct(
        public readonly Event $event,
    ) {}
}
