<?php

namespace rp\acp\page;

use rp\system\gridView\admin\CharacterGridView;
use wcf\page\AbstractGridViewPage;

/**
 * Shows a list of characters. 
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @extends AbstractGridViewPage<CharacterGridView>
 */
final class CharacterListPage extends AbstractGridViewPage
{
    public $activeMenuItem = 'rp.acp.menu.link.character.list';

    public $neededPermissions = ['admin.rp.canEditCharacter'];

    #[\Override]
    protected function createGridView(): CharacterGridView
    {
        return new CharacterGridView();
    }
}
