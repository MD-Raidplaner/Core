<?php

namespace rp\event\skill;

use rp\system\skill\SkillItem;
use wcf\event\IPsr14Event;

/**
 * Requests the collection of skill items.
 *
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
final class SkillCollecting implements IPsr14Event
{
    /**
     * @var SkillItem[]
     */
    private array $skills = [];

    /**
     * Returns the registered skills.
     *
     * @return SkillItem[]
     */
    public function getSkills(): array
    {
        return $this->skills;
    }

    /**
     * Registers a skill item.
     */
    public function register(SkillItem $skill): void
    {
        if (\array_key_exists($skill->identifier, $this->skills)) {
            throw new \InvalidArgumentException(\sprintf(
                'Skill with identifier %s already exists',
                $skill->identifier
            ));
        }

        $this->skills[$skill->identifier] = $skill;
    }
}