<?php

namespace rp\event\character;

use rp\data\character\Character;
use wcf\event\IPsr14Event;

/**
 * Indicates that a character has been created.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
final class CharacterCreated implements IPsr14Event
{
    public function __construct(
        private readonly Character $character,
        private readonly array $formData
    ) {}
}
