<?php

namespace rp\system\cache\eager;

use rp\data\role\RoleList;
use rp\system\cache\eager\data\RoleCacheData;
use wcf\system\cache\eager\AbstractEagerCache;

/**
 * Eager cache implementation for roles.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @extends AbstractEagerCache<RoleCacheData>
 */
final class RoleCache extends AbstractEagerCache
{
    public function __construct(
        private readonly int $gameID = \RP_CURRENT_GAME_ID
    ) {}

    #[\Override]
    protected function getCacheData(): RoleCacheData
    {

        $roleList = new RoleList();
        $roleList->getConditionBuilder()->add('gameID = ?', [$this->gameID]);
        $roleList->getConditionBuilder()->add('disabled = ?', [0]);
        $roleList->readObjects();

        $identifiers = [];
        foreach ($roleList as $role) {
            $identifiers[$role->identifier] = $role->getObjectID();
        }

        return new RoleCacheData(
            $roleList->getObjects(),
            $identifiers
        );
    }
}
