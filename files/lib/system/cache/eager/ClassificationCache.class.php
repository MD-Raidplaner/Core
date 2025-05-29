<?php

namespace rp\system\cache\eager;

use rp\data\classification\ClassificationList;
use rp\system\cache\eager\data\ClassificationCacheData;
use wcf\system\cache\eager\AbstractEagerCache;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\WCF;

/**
 * Eager cache implementation for classifications.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @extends AbstractEagerCache<ClassificationCacheData>
 */
final class ClassificationCache extends AbstractEagerCache
{
    public function __construct(
        private readonly int $gameID = \RP_CURRENT_GAME_ID
    ) {}

    #[\Override]
    protected function getCacheData(): ClassificationCacheData
    {
        $classificationList = new ClassificationList();
        $classificationList->getConditionBuilder()->add('isDisabled = ?', [0]);
        $classificationList->getConditionBuilder()->add('gameID = ?', [$this->gameID]);
        $classificationList->readObjects();

        $identifiers = [];
        $races = [];
        $roles = [];
        $skills = [];

        foreach ($classificationList as $object) {
            $identifiers[$object->identifier] = $object->getObjectID();
        }

        $conditionBuilder = new PreparedStatementConditionBuilder();
        $conditionBuilder->add('classificationID IN (?)', [$classificationList->getObjectIDs()]);

        // get race classification
        $sql = "SELECT  *
                FROM    rp1_classification_to_race
                {$conditionBuilder}";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute($conditionBuilder->getParameters());
        $races = $statement->fetchMap('classificationID', 'raceID', false);

        // get role classification
        $sql = "SELECT  *
                FROM    rp1_classification_to_role
                {$conditionBuilder}";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute($conditionBuilder->getParameters());
        $roles = $statement->fetchMap('classificationID', 'roleID', false);

        // get skill classification
        $sql = "SELECT  *
                FROM    rp1_classification_to_skill
                {$conditionBuilder}";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute($conditionBuilder->getParameters());
        $skills = $statement->fetchMap('classificationID', 'skillID', false);

        return new ClassificationCacheData(
            $classificationList->getObjects(),
            $identifiers,
            $races,
            $roles,
            $skills
        );
    }
}
