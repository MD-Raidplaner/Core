<?php

namespace rp\event\interaction\admin;

use rp\system\interaction\admin\CharacterInteractions;
use wcf\event\IPsr14Event;

/**
 * Indicates that the provider for character interactions is collecting interactions.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
final class CharacterInteractionCollecting implements IPsr14Event
{
    public function __construct(
        public readonly CharacterInteractions $provider
    ) {}
}
