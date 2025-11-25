<?php

namespace rp\event\game;

use rp\system\game\Game;
use wcf\event\IPsr14Event;

/**
 * Requests the collection of all registered games.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class GameCollecting implements IPsr14Event
{
    /**
     * @var array<string, Game> $games
     */
    private array $games = [];

    public function register(Game $game): void
    {
        if (isset($this->games[$game->identifier])) {
            throw new \InvalidArgumentException("Game with identifier '{$game->identifier}' is already registered.");
        }

        $this->games[$game->identifier] = $game;
    }

    /**
     * @return array<string, Game>
     */
    public function getGames(): array
    {
        return $this->games;
    }
}
