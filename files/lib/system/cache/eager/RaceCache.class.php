<?php

namespace rp\system\cache\eager;

use rp\system\cache\eager\data\RaceCacheData;
use wcf\system\cache\eager\AbstractEagerCache;

/**
 * Eager cache implementation for races.
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @extends AbstractEagerCache<RaceCacheData>
 */
final class RaceCache extends AbstractEagerCache
{
    public function __construct(
        private readonly int $gameID = \RP_CURRENT_GAME_ID
    ) {}

    #[\Override]
    protected function getCacheData(): RaceCacheData
    {
        $raceList = new \rp\data\race\RaceList();
        $raceList->getConditionBuilder()->add('gameID = ?', [$this->gameID]);
        $raceList->readObjects();

        $identifier = [];
        foreach ($raceList as $race) {
            $identifier[$race->getIdentifier()] = $race->getObjectID();
        }

        return new RaceCacheData(
            $raceList->getObjects(),
            $identifier
        );
    }
}
