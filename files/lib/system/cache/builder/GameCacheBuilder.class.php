<?php

namespace rp\system\cache\builder;

use rp\data\game\Game;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\WCF;

/**
 * Caches game information.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class GameCacheBuilder extends AbstractCacheBuilder
{
    /**
     * @inheritDoc
     */
    public function rebuild(array $parameters): array
    {
        $data = [
            'games' => [],
            'identifier' => [],
        ];

        // get games
        $sql = "SELECT  *
                FROM    rp1_game";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute();

        /** @var Game $object */
        while ($object = $statement->fetchObject(Game::class)) {
            $data['games'][$object->gameID] = $object;
            $data['identifier'][$object->identifier] = $object->gameID;
        }

        return $data;
    }
}
