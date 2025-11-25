<?php

namespace rp\system\game;

use rp\event\game\GameCollecting;
use wcf\system\event\EventHandler;
use wcf\system\SingletonFactory;

/**
 * Loads and manages all registered games.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class GameEngine extends SingletonFactory
{
    /**
     * @var array<string, Game> $games
     */
    public array $games = [];

    #[\Override]
    protected function init(): void
    {
        $event = new GameCollecting();
        EventHandler::getInstance()->fire($event);
        $this->games = $event->getGames();
    }
}
