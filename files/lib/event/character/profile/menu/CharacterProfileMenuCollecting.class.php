<?php

namespace rp\event\character\profile\menu;

use rp\system\character\profile\menu\RegisteredCharacterProfileMenu;
use wcf\event\IPsr14Event;

/**
 * Requests the collecting of menus that should be included in the list of character menus.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class CharacterProfileMenuCollecting implements IPsr14Event
{
    private \SplPriorityQueue $queue;

    public function __construct()
    {
        $this->queue = new \SplPriorityQueue();
    }

    /**
     * @return iterable<RegisteredCharacterProfileMenu>
     */
    public function getMenus(): iterable
    {
        yield from clone $this->queue;
    }

    /**
     * Registers a new menu.
     */
    public function register(string $className, int $niceValue): void
    {
        $this->queue->insert(new RegisteredCharacterProfileMenu($className), -$niceValue);
    }
}
