<?php

namespace rp\acp\page;

use rp\data\point\account\I18nPointAccountList;
use wcf\page\MultipleLinkPage;

/**
 * Shows the list of point accounts.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @property    I18nPointAccountList    $objectList
 */
final class PointAccountListPage extends MultipleLinkPage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'rp.acp.menu.link.point.account.list';

    /**
     * @inheritDoc
     */
    public $neededModules = [
        'RP_POINTS_ENABLED',
        'RP_ITEM_ACCOUNT_EASYMODE_DISABLED'
    ];

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.rp.canManagePointAccount'];

    /**
     * @inheritDoc
     */
    public $objectListClassName = I18nPointAccountList::class;

    /**
     * @inheritDoc
     */
    public $sqlOrderBy = 'titleI18n ASC';
}
