<?php

namespace rp\data\character;

/**
 * Represents a list of character profiles.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 *
 * @extends CharacterList<CharacterProfile>
 */
class CharacterProfileList extends CharacterList
{
    public $sqlOrderBy = 'character_table.characterName';
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
