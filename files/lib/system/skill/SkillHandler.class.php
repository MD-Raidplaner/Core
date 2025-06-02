<?php

namespace rp\system\skill;

use rp\event\skill\SkillCollecting;
use wcf\system\event\EventHandler;
use wcf\system\SingletonFactory;

/**
 * SkillHandler is a singleton factory that manages the skills in the system.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class SkillHandler extends SingletonFactory
{
    /**
     * @var array<string, SkillItem>
     */
    private array $skills = [];

    /**
     * Returns the skill with the given skill identifier or `null` if no such skill exists.
     */
    public function getSkillByIdentifier(string $identifier): ?SkillItem
    {
        return $this->skills[$identifier] ?? null;
    }

    /**
     * Returns all skills that are currently registered.
     * 
     * @return array<string, SkillItem> 
     */
    public function getSkills(): array
    {
        return $this->skills;
    }

    #[\Override]
    protected function init(): void
    {
        $event = new SkillCollecting();
        EventHandler::getInstance()->fire($event);
        foreach ($event->getSkills() as $skill) {
            if ($skill->game !== \RP_CURRENT_GAME) {
                continue; // Only load skills for the current game
            }

            $this->skills[$skill->identifier] = $skill;
        }
    }
}
