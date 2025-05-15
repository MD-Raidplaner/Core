<?php

namespace rp\data\classification;

use rp\system\cache\builder\ClassificationCacheBuilder;
use wcf\system\SingletonFactory;

/**
 * Manages the classification cache.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class ClassificationCache extends SingletonFactory
{
    /**
     * cached classifications
     * @var Classification[]
     */
    protected array $cachedClassifications = [];

    /**
     * cached classification ids with classification identifier as key
     * @var int[]
     */
    protected array $cachedIdentifier = [];

    /**
     * /**
     * cached races ids with classification id as key
     * @var int[][]
     */
    protected array $cachedRaces = [];

    /**
     * /**
     * cached roles ids with classification id as key
     * @var int[][]
     */
    protected array $cachedRoles = [];

    /**
     * /**
     * cached skills ids with classification id as key
     * @var int[][]
     */
    protected array $cachedSkills = [];

    /**
     * Returns the classification with the given classification id or `null` if no such classification exists.
     */
    public function getClassificationByID(int $classificationID): ?Classification
    {
        return $this->cachedClassifications[$classificationID] ?? null;
    }

    /**
     * Returns the classification with the given classification identifier or `null` if no such classification exists.
     */
    public function getClassificationByIdentifier(string $identifier): ?Classification
    {
        return $this->getClassificationByID($this->cachedIdentifier[$identifier] ?? 0);
    }

    /**
     * Returns all classifications.
     * 
     * @return	Classification[]
     */
    public function getClassifications(): array
    {
        return $this->cachedClassifications;
    }

    /**
     * Returns the classifications with the given classification ids.
     * 
     * @return	Classification[]
     */
    public function getClassificationsByIDs(array $classificationIDs): array
    {
        return \array_filter(
            \array_map(fn($classificationID) => $this->getClassificationByID($classificationID), $classificationIDs),
            fn($classification) => $classification !== null
        );
    }

    /**
     * Returns the classification races.
     * 
     * @return	int[][]
     */
    public function getClassificationRaces(): array
    {
        return $this->cachedRaces;
    }

    /**
     * Returns the classification roles.
     * 
     * @return	int[][]
     */
    public function getClassificationRoles(): array
    {
        return $this->cachedRoles;
    }

    /**
     * Returns the classification skills.
     * 
     * @return	int[][]
     */
    public function getClassificationSkills(): array
    {
        return $this->cachedSkills;
    }

    /**
     * @inheritDoc
     */
    protected function init(): void
    {
        $this->cachedClassifications = ClassificationCacheBuilder::getInstance()->getData(['gameID' => RP_CURRENT_GAME_ID], 'classification');
        $this->cachedIdentifier = ClassificationCacheBuilder::getInstance()->getData(['gameID' => RP_CURRENT_GAME_ID], 'identifier');
        $this->cachedRaces = ClassificationCacheBuilder::getInstance()->getData(['gameID' => RP_CURRENT_GAME_ID], 'races');
        $this->cachedRoles = ClassificationCacheBuilder::getInstance()->getData(['gameID' => RP_CURRENT_GAME_ID], 'roles');
        $this->cachedSkills = ClassificationCacheBuilder::getInstance()->getData(['gameID' => RP_CURRENT_GAME_ID], 'skills');
    }
}
