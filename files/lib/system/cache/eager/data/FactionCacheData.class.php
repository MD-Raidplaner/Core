<?php

namespace rp\system\cache\eager\data;

use rp\data\faction\Faction;

/**
 * Faction cache data structure.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class FactionCacheData
{
    public function __construct(
        /** @var array<string, int> */
        public readonly array $identifiers,
        /** @var array<int, Faction> */
        public readonly array $factions,
    ) {}

    /**
     * Returns the faction with the given faction id or `null` if no such faction exists.
     */
    public function getFaction(int $factionID): ?Faction
    {
        return $this->factions[$factionID] ?? null;
    }

    /**
     * Returns the faction with the given faction identifier or `null` if no such faction exists.
     */
    public function getFactionByIdentifier(string $identifier): ?Faction
    {
        return $this->getFaction($this->identifiers[$identifier] ?? 0);
    }

    /**
     * Returns all factions.
     * 
     * @return array<int, Faction> 
     */
    public function getFactions(): array
    {
        return $this->factions;
    }

    /**
     * Returns the factions with the given faction ids.
     * 
     * @param array<int> $factionIDs
     * @return array<int, Faction> 
     */
    public function getFactionsByIDs(array $factionIDs): array
    {
        return \array_filter(
            \array_map(fn($factionID) => $this->getEventByID($factionID), $factionIDs),
            fn($faction) => $faction !== null
        );
    }
}
