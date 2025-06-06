<?php

namespace rp\system\event\type;

use wcf\system\cache\runtime\UserProfileRuntimeCache;
use wcf\system\WCF;

/**
 * Default event implementation for event types.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class AppointmentEventType extends DefaultEventType
{
    protected string $eventType = 'appointment';
    protected string $eventNodesPosition = 'right';

    #[\Override]
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

        return WCF::getTPL()->render(
            'rp',
            'eventAppointment',
            [
                'eventStatuses' => [
                    'accepted' => $accepted,
                    'canceled' => $canceled,
                    'maybe' => $maybe,
                ],
            ]
        );
    }

    #[\Override]
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

        return WCF::getTPL()->render(
            'rp',
            'eventAppointmentHeaderNavigation',
            [
                'myStatus' => $myStatus,
            ]
        );
    }

    #[\Override]
    public function isExpired(): bool
    {
        if ($this->getEvent()->startTime < TIME_NOW) return true;
        return false;
    }
}
