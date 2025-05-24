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
    public function __construct(
        private readonly int $gameID = \RP_CURRENT_GAME_ID
    ) {}

    #[\Override]
    protected function getCacheData(): FactionCacheData
    {
        $factionList = new FactionList();
        $factionList->getConditionBuilder()->add('isDisabled = ?', [0]);
        $factionList->getConditionBuilder()->add('gameID = ?', [$this->gameID]);
        $factionList->readObjects();

        $identifiers = [];
        foreach ($factionList->getObjects() as $faction) {
            $identifiers[$faction->identifier] = $faction->getObjectID();
        }

        return new FactionCacheData(
            $factionList->getObjects(),
            $identifiers
        );
    }
}
