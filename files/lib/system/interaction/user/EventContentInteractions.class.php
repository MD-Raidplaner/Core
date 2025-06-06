<?php

namespace rp\system\interaction\user;

use rp\data\event\Event;
use rp\data\event\ViewableEvent;
use rp\event\interaction\user\EventContentInteractionCollecting;
use rp\form\EventEditForm;
use rp\form\RaidAddForm;
use wcf\system\event\EventHandler;
use wcf\system\interaction\AbstractInteractionProvider;
use wcf\system\interaction\DeleteInteraction;
use wcf\system\interaction\Divider;
use wcf\system\interaction\EditInteraction;
use wcf\system\interaction\LinkInteraction;
use wcf\system\interaction\RestoreInteraction;
use wcf\system\interaction\RpcInteraction;
use wcf\system\interaction\SoftDeleteInteraction;
use wcf\system\WCF;

/**
 * Interaction provider for event content interactions.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
final class EventContentInteractions extends AbstractInteractionProvider
{
    public function __construct()
    {
        $this->addInteractions([
            new SoftDeleteInteraction('rp/events/%s/soft-delete', function (ViewableEvent|Event $event): bool {
                if (!$event->canTrash()) {
                    return false;
                }

                return $event->isDeleted !== 1;
            }),
            new RestoreInteraction('rp/events/%s/restore', function (ViewableEvent|Event $event): bool {
                if (!$event->canRestore() || !$event->canEditOwnEvent()) {
                    return false;
                }

                return $event->isDeleted === 1;
            }),
            new DeleteInteraction('rp/events/%s', function (ViewableEvent|Event $event): bool {
                if (!$event->canDelete()) {
                    return false;
                }

                return $event->isDeleted !== 1;
            }),
            new RpcInteraction(
                'publish',
                'rp/events/%s/publish',
                'rp.event.button.publish',
                isAvailableCallback: static function (ViewableEvent|Event $event): bool {
                    if (!WCF::getSession()->getPermission('mod.rp.canEditEvent')) {
                        return false;
                    }

                    return $event->isDisabled !== 0;
                }
            ),
            new RpcInteraction(
                'unpublish',
                'rp/events/%s/unpublish',
                'rp.event.button.unpublish',
                isAvailableCallback: static function (ViewableEvent|Event $event): bool {
                    if (!WCF::getSession()->getPermission('mod.rp.canEditEvent')) {
                        return false;
                    }

                    return $event->isDisabled !== 1;
                }
            ),
            new RpcInteraction(
                'cancel',
                'rp/events/%s/raid-cancel',
                'rp.event.raid.cancel',
                isAvailableCallback: static function (ViewableEvent|Event $event): bool {
                    if (!$event->canCancel()) {
                        return false;
                    }

                    return $event->isCancelled !== 1;
                }
            ),
            new LinkInteraction(
            'transform',
            RaidAddForm::class,
            'rp.event.raid.transform',
                isAvailableCallback: static function (ViewableEvent|Event $event): bool {
                    if (
                        $event->isRaidEvent() &&
                        !$event->raidID &&
                        $event->getController()->isLeader()
                    ) {
                        return true;
                    }

                    return false;
                }
            ),
            new Divider(),
            new EditInteraction(EventEditForm::class, function (ViewableEvent|Event $event): bool {
                return $event->canEdit() || $event->canEditOwnEvent();
            })
        ]);

        EventHandler::getInstance()->fire(
            new EventContentInteractionCollecting($this)
        );
    }

    #[\Override]
    public function getObjectClassName(): string
    {
        return Event::class;
    }
}
