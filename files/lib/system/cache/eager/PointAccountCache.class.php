<?php

namespace rp\system\cache\eager;

use rp\data\point\account\PointAccountList;
use rp\system\cache\eager\data\PointAccountCacheData;
use wcf\system\cache\eager\AbstractEagerCache;

/**
 * Eager cache implementation for items.
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @extends AbstractEagerCache<PointAccountCacheData>
 */
final class PointAccountCache extends AbstractEagerCache
{
    public function __construct(
        private readonly int $gameID = \RP_CURRENT_GAME_ID
    ) {}

    #[\Override]
    protected function getCacheData(): PointAccountCacheData
    {
        $pointAccountList = new PointAccountList();
        $pointAccountList->getConditionBuilder()->add('gameID = ?', [$this->gameID]);
        $pointAccountList->readObjects();


        return new PointAccountCacheData(
            $pointAccountList->getObjects()
        );
    }
}
