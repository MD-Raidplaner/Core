<?php

namespace rp\system\event\listener;

use wcf\event\acp\menu\item\ItemCollecting;
use wcf\system\menu\acp\AcpMenuItem;
use wcf\system\request\LinkHandler;
use wcf\system\style\FontAwesomeIcon;
use wcf\system\WCF;

/**
 * Adds the ACP menu items for the Raidplaner.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class AcpMenuItemCollectingListener
{
    public function __invoke(ItemCollecting $event): void
    {
        $this->addTopLevelItems($event);
        $this->addCharacterItems($event);
    }

    private function addCharacterItems(ItemCollecting $event): void
    {
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
    }

    private function addTopLevelItems(ItemCollecting $event): void
    {
        $event->register(
            new AcpMenuItem(
                'rp.acp.menu.link.rp',
                icon: FontAwesomeIcon::fromValues('gamepad'),
            )
        );
    }
}
