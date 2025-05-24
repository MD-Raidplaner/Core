<?php

namespace rp\system\cache\eager;

use rp\data\faction\FactionList;
use rp\system\cache\eager\data\FactionCacheData;
use wcf\system\cache\eager\AbstractEagerCache;

/**
 * Eager cache implementation for classifications.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @extends AbstractEagerCache<FactionCacheData>
 */
final class FactionCache extends AbstractEagerCache
{
    #[\Override]
    protected function getCacheData(): FactionCacheData
    {
        $identifiers = [];
        $factions = [];

        $factionList = new FactionList();
        $factionList->getConditionBuilder()->add('isDisabled = ?', [0]);
        $factionList->getConditionBuilder()->add('gameID = ?', [\RP_CURRENT_GAME_ID]);
        $factionList->readObjects();

        foreach ($factionList->getObjects() as $faction) {
            $identifiers[$faction->identifier] = $faction->getObjectID();
            $factions[$faction->getObjectID()] = $faction;
        }

        return new FactionCacheData(
            $identifiers,
            $factions
        );
    }
}
