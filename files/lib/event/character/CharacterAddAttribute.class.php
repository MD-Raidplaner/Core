<?php

namespace rp\event\character;

use wcf\event\IPsr14Event;
use wcf\system\form\builder\container\TabTabMenuFormContainer;
use wcf\system\form\builder\IFormDocument;

/**
 * Adds custom attribute to the character add form.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
final class CharacterAddAttribute implements IPsr14Event
{
    public function __construct(
        public readonly TabTabMenuFormContainer $attributeTab
    ) {}
}
