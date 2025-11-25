<?php

namespace rp\system\game;

/**
 * Represents a game.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class Game
{
    public function __construct(
        public readonly string $identifier,
        public readonly string $title = '',
    ) {}
}
