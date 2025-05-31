<?php

namespace rp\system\cache\eager;

use rp\data\raid\event\I18nRaidEventList;
use rp\system\cache\eager\data\RaidEventCacheData;
use wcf\system\cache\eager\AbstractEagerCache;

/**
 * Eager cache implementation for raid events.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @extends AbstractEagerCache<RaidEventCacheData>
 */
final class RaidEventCache extends AbstractEagerCache
{
    public function __construct(
        private readonly string $game = \RP_CURRENT_GAME
    ) {}

    #[\Override]
    protected function getCacheData(): RaidEventCacheData
    {
        $raidEventList = new I18nRaidEventList();
        $raidEventList->readObjects();

        return new RaidEventCacheData(
            $raidEventList->getObjects()
        );
    }
}
