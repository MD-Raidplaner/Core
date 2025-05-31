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
    /**
     * @var array<string, GameItem>
     */
    private array $games = [];

    /**
     * Returns the current game item.
     * 
     * @return GameItem 
     */
    public function getCurrentGame(): GameItem {
        return $this->getGameByIdentifier(\RP_CURRENT_GAME);
    }

    /**
     * Return the game with the given identifier or `null` if no such game exists.
     */
    public function getGameByIdentifier(string $identifier): ?GameItem
    {
        return $this->games[$identifier] ?? null;
    }

    /**
     * Returns all games that are currently registered.
     * 
     * @return array<string, GameItem>
     */
    public function getGames(): array
    {
        return $this->games;
    }

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
