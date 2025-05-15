<?php

namespace rp\event\character;

use rp\data\character\Character;
use rp\data\character\CharacterProfile;
use rp\data\event\Event;
use rp\system\character\AvailableCharacter;
use wcf\event\IPsr14Event;

/**
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class AvailableCharactersChecking implements IPsr14Event
{
    /**
     * available characters
     * 
     * @var AvailableCharacter[]
     */
    private array $availableCharacters = [];

    public function __construct(
        private readonly array $characters,
        private readonly Event $event
    ) {
    }

    /**
     * Returns the available characters.
     * 
     * @return  AvailableCharacter[]
     */
    public function getAvailableCharacters(): array
    {
        return $this->availableCharacters;
    }

    /**
     * Returns all available characters.
     * 
     * @return  Character[]
     */
    public function getCharacters(): array
    {
        return $this->characters;
    }

    /**
     * Return event object
     */
    public function getEvent(): Event
    {
        return $this->event;
    }

    /**
     * Sets available character.
     */
    public function setAvailableCharacter(int|string $id, AvailableCharacter $character): void
    {
        $this->availableCharacters[$id] = $character;
    }
}
