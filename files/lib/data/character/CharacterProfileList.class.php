<?php

namespace rp\data\character;

/**
 * Represents a list of character profiles.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @method  CharacterProfile    current()
 * @method  CharacterProfile[]  getObjects()
 * @method  CharacterProfile|null   search($objectID)
 * @property    CharacterProfile[]  $objects
 */
class CharacterProfileList extends CharacterList
{
    public $sqlOrderBy = 'characterName';
    public $decoratorClassName = CharacterProfile::class;

    #[\Override]
    public function readObjects(): void
    {
        if ($this->objectIDs === null) {
            $this->readObjectIDs();
        }

        parent::readObjects();
    }
}
