<?php

namespace rp\system\cache\builder;

use rp\data\classification\Classification;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\WCF;

/**
 * Caches the classification.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class ClassificationCacheBuilder extends AbstractCacheBuilder
{
    #[\Override]
    protected function rebuild(array $parameters): array
    {
        $data = [
            'identifier' => [],
            'classification' => [],
            'races' => [],
            'roles' => [],
            'skills' => [],
        ];

        // get game classification
        $sql = "SELECT  *
                FROM    rp1_classification
                WHERE   isDisabled = ?
                    AND gameID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([
            0,
            $parameters['gameID'],
        ]);

        /** @var Classification $object */
        while ($object = $statement->fetchObject(Classification::class)) {
            $data['classification'][$object->getObjectID()] = $object;
            $data['identifier'][$object->identifier] = $object->getObjectID();
        }

        $conditionBuilder = new PreparedStatementConditionBuilder();
        $conditionBuilder->add('classificationID IN (?)', [\array_keys($data['classification'])]);

        // get race classification
        $sql = "SELECT  *
                FROM    rp1_classification_to_race
                {$conditionBuilder}";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute($conditionBuilder->getParameters());
        $data['races'] = $statement->fetchMap('classificationID', 'raceID', false);

        // get role classification
        $sql = "SELECT  *
                FROM    rp1_classification_to_role
                {$conditionBuilder}";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute($conditionBuilder->getParameters());
        $data['roles'] = $statement->fetchMap('classificationID', 'roleID', false);

        // get skill classification
        $sql = "SELECT  *
                FROM    rp1_classification_to_skill
                {$conditionBuilder}";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute($conditionBuilder->getParameters());
        $data['skills'] = $statement->fetchMap('classificationID', 'skillID', false);

        return $data;
    }
}
