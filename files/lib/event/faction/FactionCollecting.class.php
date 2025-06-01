<?php

namespace rp\event\faction;

use InvalidArgumentException;
use rp\system\faction\FactionItem;
use wcf\event\IPsr14Event;

/**
 * Requests the collection of faction items.
 *
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
final class FactionCollecting implements IPsr14Event
{
    /**
     * @var FactionItem[]
     */
    private array $factions = [];

    /**
     * Returns the registered factions.
     *
     * @return FactionItem[]
     */
    public function getFactions(): array
    {
        return $this->factions;
    }

    /**
     * Registers a faction item.
     */
    public function register(FactionItem $faction): void
    {
        if (\array_key_exists($faction->identifier, $this->factions)) {
            throw new \InvalidArgumentException(\sprintf(
                'Faction with identifier %s already exists',
                $faction->identifier
            ));
        }

        $this->factions[$faction->identifier] = $faction;
    }
}
