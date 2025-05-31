<?php

namespace rp\acp\page;

use rp\system\gridView\admin\RaidEventGridView;
use wcf\page\AbstractGridViewPage;

/**
 * Shows a list of raid events.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @extends AbstractGridViewPage<RaidEventGridView>
 */
class RaidEventListPage extends AbstractGridViewPage
{
    public $activeMenuItem = 'rp.acp.menu.link.raid.event.list';
    public $neededPermissions = ['admin.rp.canManageRaidEvent'];

    #[\Override]
    protected function createGridView(): RaidEventGridView
    {
        return new RaidEventGridView();
    }
}
