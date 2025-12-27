<?php

namespace rp\data\character;

use wcf\system\WCF;

/**
 * Represents a list of characters belonging to the current user.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
class MyCharacterList extends AccessibleCharacterList
{
    public function __construct()
    {
        parent::__construct();

        $this->conditionBuilder->add('character_table.userID = ?', [WCF::getUser()->getObjectID()]);
    }
}
