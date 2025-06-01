<?php

namespace rp\event\race;

use rp\system\race\RaceItem;
use wcf\event\IPsr14Event;

/**
 * Requests the collection of race items.
 *
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
final class RaceCollecting implements IPsr14Event
{
    /**
     * @var RaceItem[]
     */
    private array $races = [];

    /**
     * Returns the registered races.
     *
     * @return RaceItem[]
     */
    public function getRaces(): array
    {
        return $this->races;
    }

    /**
     * Registers a race item.
     */
    public function register(RaceItem $race): void
    {
        if (\array_key_exists($race->identifier, $this->races)) {
            throw new \InvalidArgumentException(\sprintf(
                'Race with identifier %s already exists',
                $race->identifier
            ));
        }

        $this->races[$race->identifier] = $race;
    }
}
