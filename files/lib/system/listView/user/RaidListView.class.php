<?php

namespace rp\system\listView\user;

use rp\data\raid\RaidList;
use rp\event\listView\user\RaidListViewInitialized;
use wcf\system\listView\AbstractListView;
use wcf\system\listView\ListViewSortField;
use wcf\system\WCF;

/**
 * List view for the list of raids.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @extends AbstractListView<RaidList>
 */
final class RaidListView extends AbstractListView
{
    public function __construct(
        private readonly int $raidEventID
    ) {
        $this->addAvailableSortFields([
            new ListViewSortField('time', 'wcf.global.date'),
        ]);

        $this->setSortField('time');
        $this->setSortOrder('DESC');
        $this->setItemsPerPage(60);
    }

    #[\Override]
    protected function createObjectList(): RaidList
    {
        $raidList = new RaidList();

        if ($this->raidEventID) {
            $raidList->getConditionBuilder()->add('raidEventID = ?', [$this->raidEventID]);
        }

        return $raidList;
    }

    #[\Override]
    protected function getInitializedEvent(): RaidListViewInitialized
    {
        return new RaidListViewInitialized($this);
    }

    #[\Override]
    public function getParameters(): array
    {
        if ($this->raidEventID) {
            return [
                'raidEventID' => $this->raidEventID
            ];
        }

        return parent::getParameters();
    }

    #[\Override]
    public function renderItems(): string
    {
        return WCF::getTPL()->render('rp', 'raidListItems', ['view' => $this]);
    }
}
