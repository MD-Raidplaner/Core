<?php

namespace rp\system\cache\eager\data;

use rp\data\skill\Skill;

/**
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class SkillCacheData
{
    public function __construct(
        /** @var array<int, Skill> */
        public readonly array $skills,
        /** @var array<string, int> */
        public readonly array $identifiers
    ) {}

    /**
     * Returns the skill with the given skill id or `null` if no such skill exists.
     */
    public function getSkill(int $skillID): ?Skill
    {
        return $this->skills[$skillID] ?? null;
    }

    /**
     * Returns the skill with the given skill identifier or `null` if no such skill exists.
     */
    public function getSkillByIdentifier(string $identifier): ?Skill
    {
        return $this->getSkill($this->identifiers[$identifier] ?? 0);
    }

    /**
     * Returns all skills.
     * 
     * @return array<int, Skill>
     */
    public function getSkills(): array
    {
        return $this->skills;
    }

    /**
     * Returns the skills with the given skill ids.
     * 
     * @param array<int> $skillIDs
     * @return array<int, Skill>
     */
    public function getSkillsByIDs(array $skillIDs): array
    {
        return \array_filter(
            \array_map(fn($skillID) => $this->getSkill($skillID), $skillIDs),
            fn($skill) => $skill !== null
        );
    }
}
