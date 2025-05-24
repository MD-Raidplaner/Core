<?php

namespace rp\data\server;

use rp\system\cache\builder\ServerCacheBuilder;
use wcf\system\SingletonFactory;

/**
 * Manages the server cache.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class ServerCache extends SingletonFactory
{
    /**
     * cached server ids with server identifier as key
     * @var int[]
     */
    protected array $cachedIdentifier = [];

    /**
     * cached servers
     * @var Server[]
     */
    protected array $cachedServers = [];

    /**
     * Returns the server with the given server id or `null` if no such server exists.
     */
    public function getServerByID(int $serverID): ?Server
    {
        return $this->cachedServers[$serverID] ?? null;
    }

    /**
     * Returns the server with the given server identifier or `null` if no such server exists.
     */
    public function getServerByIdentifier(string $identifier): ?Server
    {
        return $this->getServerByID($this->cachedIdentifier[$identifier] ?? 0);
    }

    /**
     * Returns all servers.
     * 
     * @return	Server[]
     */
    public function getServers(): array
    {
        return $this->cachedServers;
    }

    /**
     * Returns the servers with the given server ids.
     * 
     * @return	Server[]
     */
    public function getServersByIDs(array $serverIDs): array
    {
        return \array_filter(
            \array_map(fn($serverID) => $this->getEventByID($serverID), $serverIDs),
            fn($server) => $server !== null
        );
    }

    #[\Override]
    protected function init(): void
    {
        $this->cachedIdentifier = ServerCacheBuilder::getInstance()->getData(['gameID' => RP_CURRENT_GAME_ID], 'identifier');
        $this->cachedServers = ServerCacheBuilder::getInstance()->getData(['gameID' => RP_CURRENT_GAME_ID], 'server');
    }
}
