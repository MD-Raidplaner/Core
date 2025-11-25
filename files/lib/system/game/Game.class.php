<?php

namespace rp\system\game;

use rp\system\faction\Faction;
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
    /**
     * @var array<string, Faction> $factions
     */
    public function __construct(
        public readonly string $identifier,
        public readonly string $title = '',
        public readonly array $factions = [],
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
