<?php

namespace rp\data\skill;

use rp\system\cache\builder\SkillCacheBuilder;
use wcf\system\SingletonFactory;

/**
 * Manages the skill cache.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class SkillCache extends SingletonFactory
{
    /**
     * cached skill ids with skill identifier as key
     * @var int[]
     */
    protected array $cachedIdentifier = [];

    /**
     * cached skills
     * @var Skill[]
     */
    protected array $cachedSkills = [];

    /**
     * Returns the skill with the given skill id or `null` if no such skill exists.
     */
    public function getSkillByID(int $skillID): ?Skill
    {
        return $this->cachedSkills[$skillID] ?? null;
    }

    /**
     * Returns the skill with the given skill identifier or `null` if no such skill exists.
     */
    public function getSkillByIdentifier(string $identifier): ?Skill
    {
        return $this->getSkillByID($this->cachedIdentifier[$identifier] ?? 0);
    }

    /**
     * Returns all skills.
     * 
     * @return	Skill[]
     */
    public function getSkills(): array
    {
        return $this->cachedSkills;
    }

    /**
     * Returns the skills with the given skill ids.
     * 
     * @return	Skill[]
     */
    public function getSkillsByIDs(array $skillIDs): array
    {
        return \array_filter(
            \array_map(fn($skillID) => $this->getSkillByID($skillID), $skillIDs),
            fn($skill) => $skill !== null
        );
    }

    /**
     * @inheritDoc
     */
    protected function init(): void
    {
        $this->cachedIdentifier = SkillCacheBuilder::getInstance()->getData(['gameID' => RP_CURRENT_GAME_ID], 'identifier');
        $this->cachedSkills = SkillCacheBuilder::getInstance()->getData(['gameID' => RP_CURRENT_GAME_ID], 'skill');
    }
}
