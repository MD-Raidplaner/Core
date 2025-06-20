<?php

namespace rp\system\game;

use wcf\system\WCF;

/**
 * Represents a game item with a unique identifier.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class GameItem
{
    public function __construct(
        public readonly string $identifier,
    ) {}

    /**
     * Returns the title of the game.
     */
    public function getTitle(): string
    {
        return WCF::getLanguage()->get(
            \sprintf(
                'rp.game.%s',
                $this->identifier
            )
        );
    }

    public function __toString()
    {
        return $this->getTitle();
    }
}
