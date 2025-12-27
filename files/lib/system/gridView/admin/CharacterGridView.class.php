<?php

namespace rp\system\gridView\admin;

use rp\data\character\AccessibleCharacterList;
use rp\data\character\Character;
use wcf\system\gridView\AbstractGridView;
use wcf\system\WCF;

/**
 * Grid view for a list of characters.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @extends AbstractGridView<Character, AccessibleCharacterList>
 */
final class CharacterGridView extends AbstractGridView
{
    public function __construct() {}

    #[\Override]
    protected function createObjectList(): AccessibleCharacterList
    {
        return new AccessibleCharacterList();
    }

    #[\Override]
    public function isAccessible(): bool
    {
        return WCF::getSession()->getPermission('admin.rp.canEditCharacter');
    }
}
