<?php

namespace rp\system\game;

use rp\event\game\GameCollecting;
use wcf\system\event\EventHandler;
use wcf\system\SingletonFactory;

/**
 *
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class GameHandler extends SingletonFactory
{
    private array $games = [];

    #[\Override]
    protected function init()
    {
        $event = new GameCollecting();
        EventHandler::getInstance()->fire($event);
        foreach ($event->getGames() as $game) {
            $this->games[$game->identifier] = $game;
        }
    }
}
