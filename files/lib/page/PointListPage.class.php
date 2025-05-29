<?php

namespace rp\page;

use rp\system\cache\eager\PointAccountCache;
use rp\system\listView\user\PointListView;
use wcf\page\AbstractListViewPage;
use wcf\system\WCF;

/**
 * Shows the point list page.
 *
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @extends AbstractListViewPage<PointListView>
 */
class PointListPage extends AbstractListViewPage
{
    #[\Override]
    public function assignVariables(): void
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'pointAccounts' => (new PointAccountCache())->getCache()->getAccounts(),
        ]);
    }

    #[\Override]
    protected function createListView(): PointListView
    {
        return new PointListView();
    }
}
