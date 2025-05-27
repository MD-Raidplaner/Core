<?php

namespace rp\acp\page;

use rp\system\gridView\admin\PointAccountGridView;
use wcf\page\AbstractGridViewPage;

/**
 * Shows the list of point accounts.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @extends AbstractGridViewPage<PointAccountGridView>
 */
final class PointAccountListPage extends AbstractGridViewPage
{
    public $activeMenuItem = 'rp.acp.menu.link.point.account.list';
    public $neededModules = [
        'RP_POINTS_ENABLED',
        'RP_ITEM_ACCOUNT_EASYMODE_DISABLED'
    ];
    public $neededPermissions = ['admin.rp.canManagePointAccount'];

    #[\Override]
    protected function createGridView(): PointAccountGridView
    {
        return new PointAccountGridView();
    }
}
