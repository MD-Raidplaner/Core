<?php

namespace rp\data\raid\event;

use wcf\data\I18nDatabaseObjectList;

/**
 * I18n implementation of raid event list.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @method  RaidEvent       current()
 * @method  RaidEvent[]     getObjects()
 * @method  RaidEvent|null      search($objectID)
 * @property    RaidEvent[]     $objects
 */
class I18nRaidEventList extends I18nDatabaseObjectList
{
    public $i18nFields = ['title' => 'titleI18n'];
    public $className = RaidEvent::class;

    public function __construct()
    {
        parent::__construct();

        $this->conditionBuilder->add('raid_event.gameID = ?', [\RP_CURRENT_GAME]);
    }
}
