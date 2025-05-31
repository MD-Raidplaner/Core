<?php

use wcf\event\acp\menu\item\ItemCollecting;
use wcf\system\event\EventHandler;
use wcf\system\menu\acp\AcpMenuItem;
use wcf\system\request\LinkHandler;
use wcf\system\style\FontAwesomeIcon;
use wcf\system\WCF;

/**
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */

return static function (): void {
    $eventHandler = EventHandler::getInstance();

    $eventHandler->register(
        \rp\event\character\profile\menu\CharacterProfileMenuCollecting::class,
        static function (\rp\event\character\profile\menu\CharacterProfileMenuCollecting $event) {
            $event->register(\rp\system\character\profile\menu\AboutCharacterProfileMenu::class, -100);
            $event->register(\rp\system\character\profile\menu\PointsCharacterProfileMenu::class, -75);
        }
    );

    $eventHandler->register(
        \wcf\event\endpoint\ControllerCollecting::class,
        static function (\wcf\event\endpoint\ControllerCollecting $event) {
            $event->register(new \wcf\system\endpoint\controller\rp\attendees\CreateAttendee);
            $event->register(new \wcf\system\endpoint\controller\rp\attendees\DeleteAttendee);
            $event->register(new \wcf\system\endpoint\controller\rp\attendees\RenderAttendee);
            $event->register(new \wcf\system\endpoint\controller\rp\attendees\UpdateAttendeeStatus);
            $event->register(new \wcf\system\endpoint\controller\rp\characters\DeleteCharacter);
            $event->register(new \wcf\system\endpoint\controller\rp\characters\DisableCharacter);
            $event->register(new \wcf\system\endpoint\controller\rp\characters\EnableCharacter);
            $event->register(new \wcf\system\endpoint\controller\rp\characters\GetCharacterPopover);
            $event->register(new \wcf\system\endpoint\controller\rp\characters\SetPrimaryCharacter);
            $event->register(new \wcf\system\endpoint\controller\rp\events\AvailableCharacters);
            $event->register(new \wcf\system\endpoint\controller\rp\events\CancelEvent);
            $event->register(new \wcf\system\endpoint\controller\rp\events\DeleteEvent);
            $event->register(new \wcf\system\endpoint\controller\rp\events\EnableDisableEvent);
            $event->register(new \wcf\system\endpoint\controller\rp\events\RestoreEvent);
            $event->register(new \wcf\system\endpoint\controller\rp\events\TrashEvent);
            $event->register(new \wcf\system\endpoint\controller\rp\items\Tooltip);
            $event->register(new \wcf\system\endpoint\controller\rp\items\SearchItem);
            $event->register(new \wcf\system\endpoint\controller\rp\point\accounts\DeleteAccount);
            $event->register(new \wcf\system\endpoint\controller\rp\raids\events\DeleteEvent);
        }
    );

    $eventHandler->register(ItemCollecting::class, static function (ItemCollecting $event) {
        $event->register(
            new AcpMenuItem(
                'rp.acp.menu.link.rp',
                icon: FontAwesomeIcon::fromValues('gamepad'),
            )
        );

        $event->register(
            new AcpMenuItem(
                'rp.acp.menu.link.character',
                parentMenuItem: 'rp.acp.menu.link.rp',
            )
        );
        if (WCF::getSession()->getPermission('admin.rp.canEditCharacter')) {
            $event->register(
                new AcpMenuItem(
                    'rp.acp.menu.link.character.list',
                    parentMenuItem: 'rp.acp.menu.link.character',
                    link: LinkHandler::getInstance()->getControllerLink(\rp\acp\page\CharacterListPage::class),
                )
            );
            if (WCF::getSession()->getPermission('admin.rp.canAddCharacter')) {
                $event->register(
                    new AcpMenuItem(
                        'rp.acp.menu.link.character.add',
                        WCF::getLanguage()->get('rp.acp.menu.link.character.add'),
                        'rp.acp.menu.link.character.list',
                        LinkHandler::getInstance()->getControllerLink(\rp\acp\form\CharacterAddForm::class),
                        FontAwesomeIcon::fromValues('plus'),
                    )
                );
            }
        }

        $event->register(
            new AcpMenuItem(
                'rp.acp.menu.link.raid',
                parentMenuItem: 'rp.acp.menu.link.rp',
            )
        );
        if (WCF::getSession()->getPermission('admin.rp.canManageRaidEvent')) {
            $event->register(
                new AcpMenuItem(
                    'rp.acp.menu.link.raid.event.list',
                    parentMenuItem: 'rp.acp.menu.link.raid',
                    link: LinkHandler::getInstance()->getControllerLink(\rp\acp\page\RaidEventListPage::class),
                )
            );
            $event->register(
                new AcpMenuItem(
                    'rp.acp.menu.link.raid.event.add',
                    WCF::getLanguage()->get('rp.acp.menu.link.raid.event.add'),
                    'rp.acp.menu.link.raid.event.list',
                    LinkHandler::getInstance()->getControllerLink(\rp\acp\form\RaidEventAddForm::class),
                    FontAwesomeIcon::fromValues('plus'),
                )
            );
        }
        if (
            WCF::getSession()->getPermission('admin.rp.canManagePointAccount') &&
            RP_POINTS_ENABLED && RP_ITEM_ACCOUNT_EASYMODE_DISABLED
        ) {
            $event->register(
                new AcpMenuItem(
                    'rp.acp.menu.link.point.account.list',
                    parentMenuItem: 'rp.acp.menu.link.raid',
                    link: LinkHandler::getInstance()->getControllerLink(\rp\acp\page\PointAccountListPage::class),
                )
            );
            $event->register(
                new AcpMenuItem(
                    'rp.acp.menu.link.point.account.add',
                    WCF::getLanguage()->get('rp.acp.menu.link.point.account.add'),
                    'rp.acp.menu.link.point.account.list',
                    LinkHandler::getInstance()->getControllerLink(\rp\acp\form\PointAccountAddForm::class),
                    FontAwesomeIcon::fromValues('plus'),
                )
            );
        }
    });
};
