<?php

namespace rp\event\raid\character;

use wcf\event\IPsr14Event;

/**
 * Requests the collection of characters for the raid add form.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class CharacterCollecting implements IPsr14Event
{
    private array $characters = [];

    /**
     * Return registered characters
     */
    public function getCharacters(): array
    {
        return $this->characters;
    }

    /**
     * Registers a new Character
     */
    public function register(array $character): void
    {
        $this->characters[] = $character;
    }
}
