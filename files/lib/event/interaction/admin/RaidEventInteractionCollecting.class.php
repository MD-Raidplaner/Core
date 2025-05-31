<?php

namespace rp\event\interaction\admin;

use rp\system\interaction\admin\RaidEventInteractions;
use wcf\event\IPsr14Event;

/**
 * Indicates that the raid event interaction provider is collecting interactions.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class RaidEventInteractionCollecting implements IPsr14Event
{
    public function __construct(
        private readonly RaidEventInteractions $provider
    ) {}
}
