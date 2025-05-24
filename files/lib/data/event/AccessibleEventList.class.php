<?php

namespace rp\data\event;

use wcf\system\WCF;

/**
 * Represents a list of accessible events.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @method  Event   getDecoratedObject()
 * @mixin   Event
 */

class AccessibleEventList extends ViewableEventList
{
    public function __construct(int $start = 0, int $end = 0)
    {
        parent::__construct();

        if ($start && $end) {
            $this->getConditionBuilder()->add('((event.startTime >= ? AND event.startTime < ?) OR (event.startTime < ? AND event.endTime >= ?))', [$start, $end, $start, $start]);
        } else if ($start) {
            $this->getConditionBuilder()->add('(event.startTime >= ? OR (event.startTime <= ? AND event.endTime > ?))', [$start, $start, $start]);
        } else if ($end) {
            $this->getConditionBuilder()->add('event.endTime < ?', [$end]);
        }

        // default conditions
        if (!WCF::getSession()->getPermission('mod.rp.canModerateEvent')) $this->getConditionBuilder()->add('event.isDisabled = ?', [0]);
        if (!WCF::getSession()->getPermission('mod.rp.canViewDeletedEvent')) $this->getConditionBuilder()->add('event.isDeleted = ?', [0]);
    }

    [\Override]
    public function readObjects(): void
    {
        if ($this->objectIDs === null) $this->readObjectIDs();

        parent::readObjects();
    }
}
