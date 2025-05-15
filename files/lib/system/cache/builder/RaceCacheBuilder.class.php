<?php

namespace rp\system\cache\builder;

use rp\data\race\Race;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\WCF;

/**
 * Caches the race.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class RaceCacheBuilder extends AbstractCacheBuilder
{
    /**
     * @inheritDoc
     */
    protected function rebuild(array $parameters): array
    {
        $data = [
            'identifier' => [],
            'race' => [],
        ];

        // get game race
        $sql = "SELECT  *
                FROM    rp1_race
                WHERE   isDisabled = ?
                    AND gameID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([
            0,
            $parameters['gameID'],
        ]);

        /** @var Race $object */
        while ($object = $statement->fetchObject(Race::class)) {
            $data['race'][$object->raceID] = $object;
            $data['identifier'][$object->identifier] = $object->raceID;
        }

        return $data;
    }
}
