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
    public $itemsPerPage = 60;
    public $objectListClassName = I18nRaidEventList::class;
    public $sortField = 'titleI18n';
    public $sortOrder = 'ASC';
}
