<?php

namespace rp\system;

use rp\system\classification\Classification;
use rp\system\faction\Faction;
use rp\system\game\Game;
use rp\system\game\GameEngine;
use rp\system\race\Race;
use rp\system\role\Role;
use rp\system\skill\Skill;

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
     * Returns a classification by identifier for the current game or null if not found.
     */
    public static function getClassification(string $identifier): ?Classification
    {
        return self::getGame()->classifications[$identifier] ?? null;
    }

    /**
     * Returns the classifications of the current game.
     * 
     * @return array<string, Classification>
     */
    public static function getClassifications(): array
    {
        return self::getGame()->classifications;
    }

    /**
     * Returns all classifications for the given faction.
     * 
     * @return array<string, Classification>
     */
    public static function getClassificationsByFaction(string $faction): array
    {
        return \array_filter(
            self::getClassifications(),
            fn($classification): bool => \in_array($faction, $classification->factions, true)
        );
    }

    /**
     * Returns all classifications for the given race.
     * 
     *  @return array<string, Classification>
     */
    public static function getClassificationsByRace(string $race): array
    {
        return \array_filter(
            self::getClassifications(),
            fn($classification): bool => \in_array($race, $classification->races, true)
        );
    }

    /**
     * Returns all classifications for the given role.
     * 
     *  @return array<string, Classification>
     */
    public static function getClassificationsByRole(string $role): array
    {
        return \array_filter(
            self::getClassifications(),
            fn($classification): bool => \in_array($role, $classification->roles, true)
        );
    }

    /**
     * Returns all classifications for the given skill.
     * 
     *  @return array<string, Classification>
     */
    public function getClassificationsBySkill(string $skill): array
    {
        return \array_filter(
            self::getClassifications(),
            fn($classification): bool => \in_array($skill, $classification->skills, true)
        );
    }

    /**
     * Returns a faction by identifier for the current game or null if not found.
     */
    public static function getFaction(string $identifier): ?Faction
    {
        return self::getGame()->factions[$identifier] ?? null;
    }

    /**
     * Returns the factions of the current game.
     * 
     * @return array<string, Faction>
     */
    public static function getFactions(): array
    {
        return self::getGame()->factions;
    }

    /**
     * Returns the current game instance.
     */
    public static function getGame(): Game
    {
        return self::$game;
    }

    /**
     * Returns a race by identifier for the current game or null if not found.
     */
    public static function getRace(string $identifier): ?Race
    {
        return self::getGame()->races[$identifier] ?? null;
    }

    /**
     * Returns the races of the current game.
     * 
     * @return array<string, Race>
     */
    public static function getRaces(): array
    {
        return self::getGame()->races;
    }

    /**
     * Returns all races for the given faction.
     * 
     * @return array<string, Race>
     */
    public static function getRacesByFaction(string $faction): array
    {
        return \array_filter(
            self::getRaces(),
            fn($race): bool => \in_array($faction, $race->factions, true)
        );
    }

    /**
     * Returns a role by identifier for the current game or null if not found.
     */
    public static function getRole(string $identifier): ?Role
    {
        return self::getGame()->roles[$identifier] ?? null;
    }

    /**
     * Returns the roles of the current game.
     * 
     * @return array<string, Role>
     */
    public static function getRoles(): array
    {
        return self::getGame()->roles;
    }

    /**
     * Returns roles grouped by classification or for a specific classification.
     * - If no argument is given, returns all roles grouped by classification: [classificationIdentifier => [role1, role2, ...]]
     * - If a string is given, returns roles for that classification identifier
     * - If a Classification is given, returns its roles
     * 
     * @param Classification|string|null $classification    Optional classification object or identifier
     * @return array<string, array<string>>|array<string> Roles grouped by classification or roles for one classification
     */
    public static function getRolesByClassification(Classification|string|null $classification = null): array
    {
        if (\is_string($classification)) {
            return self::getClassification($classification)->roles ?? [];
        }

        if ($classification instanceof Classification) {
            return $classification->roles;
        }

        $roleMap = [];
        foreach (self::getClassifications() as $classificationItem) {
            $roleMap[$classificationItem->identifier] = $classificationItem->roles;
        }

        return $roleMap;
    }

    /**
     * Returns a skill by identifier for the current game or null if not found.
     */
    public static function getSkill(string $identifier): ?Skill
    {
        return self::getGame()->skills[$identifier] ?? null;
    }

    /**
     * Returns the skills of the current game.
     * 
     * @return array<string, Skill>
     */
    public static function getSkills(): array
    {
        return self::getGame()->skills;
    }

    /**
     * Returns skills grouped by classification or for a specific classification.
     * - If no argument is given, returns all skills grouped by classification: [classificationIdentifier => [skill1, skill2, ...]]
     * - If a string is given, returns skills for that classification identifier
     * - If a Classification is given, returns its skills
     * 
     * @param Classification|string|null $classification    Optional classification object or identifier
     * @return array<string, array<string>>|array<string> Skills grouped by classification or skills for one classification
     */
    public static function getSkillsByClassification(Classification|string|null $classification = null): array
    {
        if (\is_string($classification)) {
            return self::getClassification($classification)->skills ?? [];
        }

        if ($classification instanceof Classification) {
            return $classification->skills;
        }

        $skillMap = [];
        foreach (self::getClassifications() as $classificationItem) {
            $skillMap[$classificationItem->identifier] = $classificationItem->skills;
        }

        return $skillMap;
    }

    /**
     * Initializes the game instance.
     */
    private function initGame(): void
    {
        $games = GameEngine::getInstance()->games;
        self::$game = $games[\RP_CURRENT_GAME] ?? $games['default'];
    }
}
