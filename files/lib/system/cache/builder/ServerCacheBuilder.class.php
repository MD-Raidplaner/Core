<?php

namespace rp\system\cache\builder;

use rp\data\server\Server;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\WCF;

/**
 * Caches the server.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class ServerCacheBuilder extends AbstractCacheBuilder
{
    #[\Override]
    protected function rebuild(array $parameters): array
    {
        $data = [
            'identifier' => [],
            'server' => [],
        ];

        // get game server
        $sql = "SELECT  *
                FROM    rp1_server
                WHERE   gameID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([
            $parameters['gameID'],
        ]);

        /** @var Server $object */
        while ($object = $statement->fetchObject(Server::class)) {
            $data['server'][$object->serverID] = $object;
            $data['identifier'][$object->identifier] = $object->serverID;
        }

        return $data;
    }
}
