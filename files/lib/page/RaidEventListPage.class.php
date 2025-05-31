<?php

namespace rp\page;

use rp\system\listView\user\RaidEventListView;
use wcf\page\AbstractListViewPage;

/**
 * Shows the raid event list page.
 *
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @extends AbstractListViewPage<RaidEventListView>
 */
final class RaidEventListPage extends AbstractListViewPage
{
    #[\Override]
    protected function createListView(): RaidEventListView
    {
        return new RaidEventListView();
    }
}
