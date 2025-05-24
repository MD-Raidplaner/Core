<?php

namespace rp\system\cache\builder;

use rp\data\faction\Faction;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\WCF;

/**
 * Caches the faction.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class FactionCacheBuilder extends AbstractCacheBuilder
{
    #[\Override]
    protected function rebuild(array $parameters): array
    {
        $data = [
            'faction' => [],
            'identifier' => [],
        ];

        // get game faction
        $sql = "SELECT  *
                FROM    rp1_faction
                WHERE   isDisabled = ?
                    AND gameID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([
            0,
            $parameters['gameID'],
        ]);

        /** @var Faction $object */
        while ($object = $statement->fetchObject(Faction::class)) {
            $data['faction'][$object->factionID] = $object;
            $data['identifier'][$object->identifier] = $object->factionID;
        }

        return $data;
    }
}
