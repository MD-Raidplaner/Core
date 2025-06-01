<?php

namespace rp\system\race;

use rp\event\race\RaceCollecting;
use wcf\system\event\EventHandler;
use wcf\system\SingletonFactory;

/**
 * RaceHandler is a singleton factory that manages the races in the system.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class RaceHandler extends SingletonFactory
{
    /**
     * @var array<string, RaceItem>
     */
    private array $races = [];

    /**
     * Returns the race with the given fraction or `null` if no such race exists.
     */
    public function getRaceByFraction(string $fraction): ?RaceItem
    {
        return \array_find(
            $this->races,
            fn($race) => \in_array(
                $fraction,
                $race->fractions,
                true
            )
        );
    }

    /**
     * Returns all races that are associated with the given fractions.
     * 
     * @param array<string> $fractions
     * @return array<string, RaceItem>
     */
    public function getRacesByFractions(array $fractions): array
    {
        $races = [];

        foreach ($fractions as $fraction) {
            $race = $this->getRaceByFraction($fraction);
            if ($race !== null) {
                $races[$race->identifier] = $race;
            }
        }

        return $races;
    }

    /**
     * Returns the race with the given race identifier or `null` if no such race exists.
     */
    public function getRaceByIdentifier(string $identifier): ?RaceItem
    {
        return $this->races[$identifier] ?? null;
    }

    /**
     * Returns all races that are currently registered.
     * 
     * @return array<string, RaceItem> 
     */
    public function getRaces(): array
    {
        return $this->races;
    }

    #[\Override]
    protected function init(): void
    {
        $event = new RaceCollecting();
        EventHandler::getInstance()->fire($event);
        foreach ($event->getRaces() as $race) {
            if ($race->game !== \RP_CURRENT_GAME) {
                continue; // Only load races for the current game
            }

            $this->races[$race->identifier] = $race;
        }
    }
}
