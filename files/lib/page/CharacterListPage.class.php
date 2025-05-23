<?php

namespace rp\page;

use rp\system\listView\user\CharacterListView;
use wcf\page\AbstractListViewPage;

/**
 * Shows character list page.
 *
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @extends AbstractListViewPage<CharacterListView>
 */
class CharacterListPage extends AbstractListViewPage
{
    public $neededPermissions = ['user.rp.canViewCharacterList'];

    #[\Override]
    protected function createListView(): CharacterListView
    {
        return new CharacterListView();
    }
}
