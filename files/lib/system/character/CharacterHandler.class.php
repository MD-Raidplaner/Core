<?php

namespace rp\system\character;

use rp\data\character\CharacterProfile;
use rp\data\character\CharacterProfileList;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * Handles characters for current users.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class CharacterHandler extends SingletonFactory
{
    /**
     * Content of the characters of the current user
     * @var CharacterProfile[]
     */
    protected array $characters = [];

    /**
     * Returns the character with the given character id or `null` if no character with id exists.
     */
    public function getCharacterByID(int $characterID): ?CharacterProfile
    {
        return $this->characters[$characterID] ?? null;
    }

    /**
     * Returns list of user characters.
     *
     * @return  CharacterProfile[]
     */
    public function getCharacters(): array
    {
        return $this->characters;
    }

    /**
     * Returns primary character or `null` if no primary character exists.
     */
    public function getPrimaryCharacter(): ?CharacterProfile
    {
        return \array_reduce(
            $this->getCharacters(),
            fn ($carry, $character) => $carry ?? ($character->isPrimary ? $character : null)
        );
    }

    #[\Override]
    protected function init(): void
    {
        if (WCF::getUser()->userID) {
            $characterList = new CharacterProfileList();
            $characterList->getConditionBuilder()->add('userID = ?', [WCF::getUser()->userID]);
            $characterList->getConditionBuilder()->add('isDisabled = ?', [0]);
            $characterList->readObjects();
            $this->characters = $characterList->getObjects();
        }
    }
}
