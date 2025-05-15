<?php

namespace rp\data\modification\log;

use rp\system\log\modification\EventModificationLogHandler;
use wcf\data\modification\log\ModificationLogList;

/**
 * Represents a list of modification logs for events.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @method  ViewableEventModificationLog    current()
 * @method  ViewableEventModificationLog[]  getObjects()
 * @method  ViewableEventModificationLog|null   search($objectID)
 * @property    ViewableEventModificationLog[]  $objects
 */
class EventModificationLogList extends ModificationLogList
{
    /**
     * @inheritDoc
     */
    public $decoratorClassName = ViewableEventModificationLog::class;

    /**
     * Initializes the event modification log list.
     */
    public function setEventData(array $eventIDs, string $action = '')
    {
        $this->getConditionBuilder()->add("objectTypeID = ?", [EventModificationLogHandler::getInstance()->getObjectType()->objectTypeID]);
        $this->getConditionBuilder()->add("objectID IN (?)", [$eventIDs]);
        if (!empty($action)) {
            $this->getConditionBuilder()->add("action = ?", [$action]);
        }
    }
}
