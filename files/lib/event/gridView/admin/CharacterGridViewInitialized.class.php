<?php

namespace rp\event\gridView\admin;

use rp\system\gridView\admin\CharacterGridView;
use wcf\event\IPsr14Event;

/**
 * Indicates that the character grid view has been initialized.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class CharacterGridViewInitialized implements IPsr14Event
{
    public function __construct(
        public readonly CharacterGridView $gridView,
    ) {}
}
