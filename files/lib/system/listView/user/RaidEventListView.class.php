<?php

namespace rp\system\listView\user;

use rp\data\raid\event\I18nRaidEventList;
use rp\event\listView\user\RaidEventListViewInitialized;
use wcf\system\listView\AbstractListView;
use wcf\system\listView\ListViewSortField;
use wcf\system\WCF;

/**
 * List view for the list of raid events.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @extends AbstractListView<RaidEventList>
 */
final class RaidEventListView extends AbstractListView
{
    public function __construct() {
    $this->addAvailableSortFields([
            new ListViewSortField('title', 'wcf.global.title', 'titleI18n'),
        ]);

        $this->setSortField('title');
        $this->setItemsPerPage(60);
    }

    #[\Override]
    protected function createObjectList(): I18nRaidEventList
    {
        return new I18nRaidEventList();
    }

    #[\Override]
    protected function getInitializedEvent(): RaidEventListViewInitialized
    {
        return new RaidEventListViewInitialized($this);
    }

    #[\Override]
    public function renderItems(): string
    {
        return WCF::getTPL()->render('rp', 'raidEventListItems', ['view' => $this]);
    }
}
