<?php

namespace rp\system\cache\runtime;

use rp\data\event\ViewableEventList;
use wcf\system\cache\runtime\AbstractRuntimeCache;

/**
 * Runtime cache implementation for viewable events.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @method  ViewableEvent[] getCachedObjects()
 * @method  ViewableEvent   getObject($objectID)
 * @method  ViewableEvent[] getObjects(array $objectIDs)
 */
final class ViewableEventRuntimeCache extends AbstractRuntimeCache
{
    /**
     * @inheritDoc
     */
    protected $listClassName = ViewableEventList::class;
}
