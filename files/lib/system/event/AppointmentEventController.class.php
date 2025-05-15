<?php

namespace rp\system\event;

use wcf\system\cache\runtime\UserProfileRuntimeCache;
use wcf\system\WCF;

/**
 * Default event implementation for event controllers.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class AppointmentEventController extends DefaultEventController
{
    /**
     * @inheritDoc
     */
    protected string $eventController = 'de.md-raidplaner.rp.event.controller.appointment';

    /**
     * event nodes position
     */
    protected string $eventNodesPosition = 'right';

    /**
     * @inheritDoc
     */
    public function getContent(): string
    {
        $accepted = $canceled = $maybe =  [];
        $myStatus = "";

        foreach ($this->getEvent()->appointments ?: [] as $status => $userIDs) {
            foreach ($userIDs as $userID) {
                $user = UserProfileRuntimeCache::getInstance()->getObject($userID);

                match ($status) {
                    'accepted' => $accepted[] = $user,
                    'canceled' => $canceled[] = $user,
                    'maybe' => $maybe[] = $user,
                };
            }
        }

        return WCF::getTPL()->fetch(
            'eventAppointment',
            'rp',
            [
                'eventStatuses' => [
                    'accepted' => $accepted,
                    'canceled' => $canceled,
                    'maybe' => $maybe,
                ],
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function getContentHeaderNavigation(): string
    {
        $myStatus = "";
        foreach ($this->getEvent()->appointments ?? [] as $status => $userIDs) {
            foreach ($userIDs ?? [] as $userID) {
                $user = UserProfileRuntimeCache::getInstance()->getObject($userID);
                if ($user->userID === WCF::getUser()->userID) {
                    $myStatus = $status;
                    break 2;
                }
            }
        }

        return WCF::getTPL()->fetch(
            'eventAppointmentHeaderNavigation',
            'rp',
            [
                'myStatus' => $myStatus,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function isExpired(): bool
    {
        if ($this->getEvent()->startTime < TIME_NOW) return true;
        return false;
    }
}
