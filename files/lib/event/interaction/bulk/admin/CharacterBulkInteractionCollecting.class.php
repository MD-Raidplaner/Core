<?php

namespace rp\event\interaction\bulk\admin;

use rp\system\interaction\bulk\admin\CharacterBulkInteractions;
use wcf\event\IPsr14Event;

/**
 * Indicates that the provider for character bulk interactions is collecting interactions.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
final class CharacterBulkInteractionCollecting implements IPsr14Event
{
    public function __construct(
        public readonly CharacterBulkInteractions $provider,
    ) {}
}
