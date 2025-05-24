<?php

namespace rp\system\cache\eager\data;

use rp\data\classification\Classification;

/**
 * Classification cache data structure.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class ClassificationCacheData
{
    public function __construct(
        /** @var array<string, int> */
        public readonly array $identifiers,
        /** @var array<int, Classification> */
        public readonly array $classifications,
        /** @var array<int, list<int>> */
        public readonly array $races,
        /** @var array<int, list<int>> */
        public readonly array $roles,
        /** @var array<int, list<int>> */
        public readonly array $skills
    ) {}

    /**
     * Returns the classification with the given classification id or `null` if no such classification exists.
     */
    public function getClassification(int $classificationID): ?Classification
    {
        return $this->classifications[$classificationID] ?? null;
    }

    /**
     * Returns the classification with the given classification identifier or `null` if no such classification exists.
     */
    public function getClassificationByIdentifier(string $identifier): ?Classification
    {
        return $this->getClassification($this->identifiers[$identifier] ?? 0);
    }

    /**
     * Returns all classifications.
     * 
     * @return array<int, Classification> 
     */
    public function getClassifications(): array
    {
        return $this->classifications;
    }

    /**
     * Returns the classifications with the given classification ids.
     * 
     * @param int[] $classificationIDs
     * @return	array<int, Classification>
     */
    public function getClassificationsByIDs(array $classificationIDs): array
    {
        return \array_filter(
            \array_map(fn($classificationID) => $this->getClassification($classificationID), $classificationIDs),
            fn($classification) => $classification !== null
        );
    }

    /**
     * Returns the classification races.
     * 
     * @return array<int, list<int>>
     */
    public function getClassificationRaces(): array
    {
        return $this->races;
    }

    /**
     * Returns the classification roles.
     * 
     * @return array<int, list<int>>
     */
    public function getClassificationRoles(): array
    {
        return $this->roles;
    }

    /**
     * Returns the classification skills.
     * 
     * @return array<int, list<int>>
     */
    public function getClassificationSkills(): array
    {
        return $this->skills;
    }
}
