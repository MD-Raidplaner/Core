<?php

namespace rp\system\game;

use wcf\system\WCF;

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

    /**
     * Gets the title of the game.
     */
    public function getTitle(): string
    {
        return $this->title ?: WCF::getLanguage()->get(\sprintf('rp.game.%s', $this->identifier));
    }

    public function __toString(): string
    {
        return $this->getTitle();
    }
}
