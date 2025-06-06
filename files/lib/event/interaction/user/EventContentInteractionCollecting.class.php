<?php

namespace rp\event\interaction\user;

use rp\system\interaction\user\EventContentInteractions;
use wcf\event\IPsr14Event;

/**
 * Indicates that the process of collecting event content interactions has started.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class EventContentInteractionCollecting implements IPsr14Event
{
    public function __construct(public readonly EventContentInteractions $provider) {}
}
