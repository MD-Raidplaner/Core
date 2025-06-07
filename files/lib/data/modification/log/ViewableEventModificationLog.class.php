<?php

namespace rp\data\modification\log;

use rp\data\event\Event;
use wcf\data\DatabaseObjectDecorator;
use wcf\data\modification\log\IViewableModificationLog;
use wcf\data\modification\log\ModificationLog;
use wcf\system\WCF;

/**
 * Provides a viewable event modification log.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @mixin   ModificationLog
 * @extends DatabaseObjectDecorator<ModificationLog>
 */
class ViewableEventModificationLog extends DatabaseObjectDecorator implements IViewableModificationLog
{
    protected static $baseClass = ModificationLog::class;
    public ?Event $event = null;

    /**
     * Sets the event object.
     */
    public function setEvent(Event $event): void
    {
        if ($event->eventID === $this->objectID) {
            $this->event = $event;
        }
    }

    #[\Override]
    public function getAffectedObject(): ?Event
    {
        return $this->event;
    }

    /**
     * Returns readable representation of current log entry.
     * 
     * @return	string
     */
    public function __toString()
    {
        return WCF::getLanguage()->getDynamicVariable('rp.event.log.' . $this->action, [
            'additionalData' => $this->additionalData,
            'event' => $this->event
        ]);
    }
}
