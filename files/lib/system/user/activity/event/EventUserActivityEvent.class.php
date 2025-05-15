<?php

namespace rp\system\user\activity\event;

use rp\system\cache\runtime\ViewableEventRuntimeCache;
use wcf\system\SingletonFactory;
use wcf\system\user\activity\event\IUserActivityEvent;
use wcf\system\WCF;

/**
 * This class extends the main WCF class by raidplaner specific functions.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class EventUserActivityEvent  extends SingletonFactory implements IUserActivityEvent 
{
    /**
     * @inheritDoc
     */
    public function prepare(array $events)
    {
        $objectIDs = [];
        foreach ($events as $event) {
            $objectIDs[] = $event->objectID;
        }

        ViewableEventRuntimeCache::getInstance()->cacheObjectIDs($objectIDs);

        // set message
        foreach ($events as $event) {
            $eventObj = ViewableEventRuntimeCache::getInstance()->getObject($event->objectID);
            if ($eventObj !== null) {
                if ($eventObj->canRead()) {
                    $event->setIsAccessible();

                    $event->setTitle(WCF::getLanguage()->getDynamicVariable(
                        'rp.event.recentActivity',
                        [
                            'eventObj' => $eventObj,
                            'author' => $event->getUserProfile(),
                        ]
                    ));
                    $event->setDescription(\strip_tags($eventObj->getExcerpt(500)), true);
                    $event->setLink($eventObj->getLink());
                }
            } else {
                $event->setIsOrphaned();
            }
        }
    }
}