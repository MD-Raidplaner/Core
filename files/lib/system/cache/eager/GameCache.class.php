<?php

namespace rp\system\cache\eager;

use rp\data\game\GameList;
use rp\system\cache\eager\data\GameCacheData;
use wcf\system\cache\eager\AbstractEagerCache;

/**
 * Eager cache implementation for games.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @extends AbstractEagerCache<GameCacheData>
 */
final class GameCache extends AbstractEagerCache
{
    #[\Override]
    protected function getCacheData(): GameCacheData
    {
        $gameList = new GameList();
        $gameList->readObjects();

        $identifiers = [];
        foreach ($gameList->getObjects() as $game) {
            $identifiers[$game->identifier] = $game->gameID;
        }

        return new GameCacheData(
            $gameList->getObjects(),
            $identifiers
        );
    }
}
