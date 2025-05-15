<?php

namespace rp\acp\page;

use rp\data\raid\event\I18nRaidEventList;
use wcf\page\MultipleLinkPage;

/**
 * Shows a list of raid events.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @property    I18nRaidEventList   $objectList
 */
class RaidEventListPage extends MultipleLinkPage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'rp.acp.menu.link.raid.event.list';

    /**
     * @inheritDoc
     */
    public $itemsPerPage = 50;

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.rp.canManageRaidEvent'];

    /**
     * @inheritDoc
     */
    public $objectListClassName = I18nRaidEventList::class;

    /**
     * @inheritDoc
     */
    public $sqlOrderBy = 'titleI18n ASC';

    /**
     * @inheritDoc
     */
    protected function initObjectList(): void
    {
        parent::initObjectList();

        if (!empty($this->objectList->sqlSelects)) {
            $this->objectList->sqlSelects .= ',';
        }
        $this->objectList->sqlSelects .= 'point_account.title as pointAccountName';
        $this->objectList->sqlJoins .= " LEFT JOIN rp" . WCF_N . "_point_account point_account ON (point_account.accountID = raid_event.pointAccountID)";
    }
}
