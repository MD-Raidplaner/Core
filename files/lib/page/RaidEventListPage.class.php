<?php

namespace rp\page;

use rp\data\raid\event\I18nRaidEventList;
use wcf\page\MultipleLinkPage;

/**
 * Shows the raids page.
 *
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class RaidEventListPage extends MultipleLinkPage
{
    /**
     * @inheritDoc
     */
    public $itemsPerPage = 60;

    /**
     * @inheritDoc
     */
    public $objectListClassName = I18nRaidEventList::class;

    /**
     * @inheritDoc
     */
    public $sortField = 'titleI18n';

    /**
     * @inheritDoc
     */
    public $sortOrder = 'ASC';
}
