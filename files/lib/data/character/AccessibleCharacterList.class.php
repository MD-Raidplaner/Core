<?php

namespace rp\data\character;

/**
 * Represents a list of accessible characters.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
class AccessibleCharacterList extends CharacterList
{
    public function __construct()
    {
        parent::__construct();

        $this->conditionBuilder->add('character_table.game = ?', [\RP_CURRENT_GAME]);
    }
}
