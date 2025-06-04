<?php

namespace rp\system\cache\eager;

use rp\data\server\ServerList;
use rp\system\cache\eager\data\ServerCacheData;
use wcf\system\cache\eager\AbstractEagerCache;

/**
 * Eager cache implementation for servers.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @extends AbstractEagerCache<ServerCacheData>
 */
final class ServerCache extends AbstractEagerCache
{
    public function __construct(
        private readonly string $game = \RP_CURRENT_GAME
    ) {}

    #[\Override]
    protected function getCacheData(): ServerCacheData
    {
        $serverList = new ServerList();
        $serverList->getConditionBuilder()->add('game = ?', [$this->game]);
        $serverList->readObjects();

        $identifiers = [];
        foreach ($serverList as $server) {
            $identifiers[$server->identifier] = $server->getObjectID();
        }

        return new ServerCacheData(
            $serverList->getObjects(),
            $identifiers
        );
    }
}
