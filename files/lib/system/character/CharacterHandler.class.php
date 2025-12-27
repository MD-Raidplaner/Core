<?php

namespace rp\system\character;

use rp\data\character\Character;
use rp\data\character\MyCharacterList;
use wcf\system\SingletonFactory;
use wcf\system\WCF;
use Zend\Console\Prompt\Char;

/**
 *
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class CharacterHandler extends SingletonFactory
{
    /**
     * Cached characters of the current user
     * @var array<int, Character>
     */
    private array $characters = [];

    /**
     * Returns the character with the given id or null if no such character exists.
     */
    public function getCharacter(int $characterID): ?Character
    {
        return $this->characters[$characterID] ?? null;
    }

    /**
     * Returns all characters of the current user.
     * 
     * @return array<int, Character>
     */
    public function getCharacters(): array
    {
        return $this->characters;
    }

    /**
     * Returns the primary character of the current user or null if no such character exists.
     */
    public function getPrimaryCharacter(): ?Character
    {
        return \array_reduce(
            $this->getCharacters(),
            fn($carry, $character) => $carry ?? ($character->isPrimary ? $character : null)
        );
    }

    #[\Override]
    protected function init(): void
    {
        if (WCF::getUser()->getObjectID()) {
            $characterList = new MyCharacterList();
            $characterList->getConditionBuilder()->add('character_table.isDisabled = ?', [0]);
            $characterList->readObjects();
            $this->characters = $characterList->getObjects();
        }
    }
}
