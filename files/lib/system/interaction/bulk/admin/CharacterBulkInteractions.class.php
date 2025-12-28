<?php

namespace rp\system\interaction\bulk\admin;

use rp\data\character\CharacterProfile;
use rp\data\character\CharacterProfileList;
use rp\event\interaction\bulk\admin\CharacterBulkInteractionCollecting;
use wcf\system\event\EventHandler;
use wcf\system\interaction\bulk\AbstractBulkInteractionProvider;
use wcf\system\interaction\bulk\BulkDeleteInteraction;

/**
 * Bulk interaction provider for characters.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class CharacterBulkInteractions extends AbstractBulkInteractionProvider
{
    public function __construct()
    {
        $this->addInteractions([
            new BulkDeleteInteraction('rp/core/characters/%s', static function (CharacterProfile $character): bool {
                return $character->canDelete();
            }),
        ]);

        EventHandler::getInstance()->fire(
            new CharacterBulkInteractionCollecting($this)
        );
    }

    #[\Override]
    public function getObjectListClassName(): string
    {
        return CharacterProfileList::class;
    }
}
