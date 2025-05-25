<?php

namespace rp\system\cache\eager\data;

use rp\data\race\Race;

/**
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class RaceCacheData
{
    public function __construct(
        /** @var array<int, Race> */
        public readonly array $races,
        /** @var array<string, int> */
        public readonly array $identifier
    ) {}

    /**
     * Returns the race with the given race id or `null` if no such race exists.
     */
    public function getRace(int $raceID): ?Race
    {
        return $this->races[$raceID] ?? null;
    }

    /**
     * Returns the race with the given race identifier or `null` if no such race exists.
     */
    public function getRaceByIdentifier(string $identifier): ?Race
    {
        return $this->getRace($this->identifier[$identifier] ?? 0);
    }

    /**
     * Returns all races.
     * 
     * @return array<int, Race>
     */
    public function getRaces(): array
    {
        return $this->races;
    }

    /**
     * Returns the races with the given race ids.
     * 
     * @param array<int> $raceIDs
     * @return array<int, Race>
     */
    public function getRacesByIDs(array $raceIDs): array
    {
        return \array_filter(
            \array_map(fn($raceID) => $this->getRace($raceID), $raceIDs),
            fn($race) => $race !== null
        );
    }
}
