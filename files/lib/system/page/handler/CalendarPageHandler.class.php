<?php

namespace rp\system\page\handler;

use rp\data\event\ViewableEvent;
use wcf\system\page\handler\AbstractMenuPageHandler;
use wcf\system\WCF;

/**
 * Page handler implementation for the calendar.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class CalendarPageHandler extends AbstractMenuPageHandler
{
    #[\Override]
    public function getOutstandingItemCount($objectID = null): int
    {
        return ViewableEvent::getUnreadEvents();
    }

    #[\Override]
    public function isVisible($objectID = null): bool
    {
        return WCF::getSession()->getPermission('user.rp.canReadEvent');
    }
}
