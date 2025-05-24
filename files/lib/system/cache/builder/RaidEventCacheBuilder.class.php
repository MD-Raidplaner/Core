<?php

namespace rp\system\cache\builder;

use rp\data\raid\event\I18nRaidEventList;
use wcf\system\cache\builder\AbstractCacheBuilder;

/**
 * Caches the raid event.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class RaidEventCacheBuilder extends AbstractCacheBuilder
{
    #[\Override]
    protected function rebuild(array $parameters): array
    {
        $list = new I18nRaidEventList();
        $list->readObjects();
        return $list->getObjects();
    }
}
