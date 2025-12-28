<?php

namespace rp\event\character;

use rp\data\character\Character;
use wcf\event\IPsr14Event;
use wcf\system\form\builder\IFormDocument;

/**
 * Indicates that character edit data is available.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
final class CharacterEditData implements IPsr14Event
{
    public function __construct(
        public readonly IFormDocument $form,
        public readonly Character $character,
    ) {}
}
