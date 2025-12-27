<?php

namespace rp\event\character;

use wcf\event\IPsr14Event;
use wcf\system\form\builder\IFormDocument;

/**
 * Add custom fields to the character create form.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
final class CharacterAddCreateForm implements IPsr14Event
{
    public function __construct(
        public readonly IFormDocument $form
    ) {}
}
