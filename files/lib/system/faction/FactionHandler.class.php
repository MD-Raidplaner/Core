<?php

namespace rp\system\faction;

use rp\event\faction\FactionCollecting;
use wcf\system\event\EventHandler;
use wcf\system\SingletonFactory;

/**
 * FactionHandler is a singleton factory that manages the factions in the system.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class FactionHandler extends SingletonFactory
{
    /**
     * @var array<string, FactionItem>
     */
    private array $factions = [];

    /**
     * Returns the faction with the given faction identifier or `null` if no such faction exists.
     */
    public function getFactionByIdentifier(string $identifier): ?FactionItem
    {
        return $this->factions[$identifier] ?? null;
    }

    /**
     * Returns all factions that are currently registered.
     * 
     * @return array<string, FactionItem> 
     */
    public function getFactions(): array
    {
        return $this->factions;
    }

    #[\Override]
    protected function init(): void
    {
        $event = new FactionCollecting();
        EventHandler::getInstance()->fire($event);
        foreach ($event->getFactions() as $faction) {
            if ($faction->game !== \RP_CURRENT_GAME) {
                continue; // Only load factions for the current game
            }

            $this->factions[$faction->identifier] = $faction;
        }
    }
}
