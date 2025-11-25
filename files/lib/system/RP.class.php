<?php

namespace rp\system;

use rp\system\game\Game;
use rp\system\game\GameEngine;

/**
 * This class provides core functionalities for the MD-Raidplaner application.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class RP
{
    /**
     * Game instance
     */
    private static Game $game;

    public function __construct()
    {
        $this->initGame();
    }

    /**
     * Returns the current game instance.
     */
    public static function getGame(): Game
    {
        return self::$game;
    }

    /**
     * Initializes the game instance.
     */
    private function initGame(): void
    {
        $games = GameEngine::getInstance()->games;

        self::$game = $games['default'];
    }
}
