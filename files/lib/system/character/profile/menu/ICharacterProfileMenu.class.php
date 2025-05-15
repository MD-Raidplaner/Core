<?php

namespace rp\system\character\profile\menu;

/**
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
interface ICharacterProfileMenu
{
    /**
     * Returns content for this character menu item.
     */
    public function getContent(int $characterID): string;

    /**
     * Returns true if the associated menu item should be visible for the active user.
     */
    public function isVisible(int $characterID): bool;
}
