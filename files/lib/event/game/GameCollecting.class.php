<?php

namespace rp\event\game;

use rp\system\game\GameItem;
use wcf\event\IPsr14Event;

/**
 * Requests the collection of game items.
 *
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
final class GameCollecting implements IPsr14Event
{
    /**
     * @var GameItem[]
     */
    private array $games = [];

    /**
     * @return GameItem[]
     */
    public function getGames(): array
    {
        return $this->games;
    }

    public function register(GameItem $game): void
    {
        $this->games[] = $game;
    }
}
