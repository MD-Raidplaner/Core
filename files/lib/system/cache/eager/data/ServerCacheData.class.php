<?php

namespace rp\system\cache\eager\data;

use rp\data\server\Server;

/**
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class ServerCacheData
{
    public function __construct(
        /** @var array<int, Server> */
        public readonly array $servers,
        /** @var array<string, int> */
        public readonly array $identifiers
    ) {}

    /**
     * Returns the server with the given server id or `null` if no such server exists.
     */
    public function getServer(int $serverID): ?Server
    {
        return $this->servers[$serverID] ?? null;
    }

    /**
     * Returns the server with the given server identifier or `null` if no such server exists.
     */
    public function getServerByIdentifier(string $identifier): ?Server
    {
        return $this->getServer($this->identifiers[$identifier] ?? 0);
    }

    /**
     * Returns all servers.
     * 
     * @return array<int, Server>
     */
    public function getServers(): array
    {
        return $this->servers;
    }

    /**
     * Returns the servers with the given server ids.
     * 
     * @param array<int> $serverIDs
     * @return array<int, Server>
     */
    public function getServersByIDs(array $serverIDs): array
    {
        return \array_filter(
            \array_map(fn($serverID) => $this->getServer($serverID), $serverIDs),
            fn($server) => $server !== null
        );
    }
}
