<?php

namespace rp\system\game;

use rp\system\classification\Classification;
use rp\system\faction\Faction;
use rp\system\race\Race;
use rp\system\role\Role;
use rp\system\skill\Skill;
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
     * @param array<string, Faction> $factions
     * @param array<string, Race> $races
     * @param array<string, Role> $roles
     * @param array<string, Classification> $classifications
     * @param array<string, Skill> $skills
     */
    public function __construct(
        public readonly string $identifier,
        public readonly string $title = '',
        public readonly array $factions = [],
        public readonly array $races = [],
        public readonly array $roles = [],
        public readonly array $classifications = [],
        public readonly array $skills = []
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
