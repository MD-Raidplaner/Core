<?php

namespace rp\event\character;

use rp\data\character\Character;
use wcf\event\IPsr14Event;

/**
 * Indicates that multiple characters has been deleted.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @property-read Character[] $characters
 */
final class CharactersDeleted implements IPsr14Event
{
    public function __construct(
        public readonly array $characters,
    ) {}
}
