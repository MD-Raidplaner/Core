<?php

namespace rp\system\character\profile\menu;

use rp\system\cache\runtime\CharacterProfileRuntimeCache;
use wcf\system\WCF;

/**
 * Character menu implementation for about content.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class AboutCharacterProfileMenu implements ICharacterProfileMenu
{
    /**
     * @inheritDoc
     */
    public function getContent(int $characterID): string
    {
        $character = CharacterProfileRuntimeCache::getInstance()->getObject($characterID);

        return WCF::getTPL()->fetch('characterProfileAbout', 'rp', [
            'notes' => $character->notes ?? '',
        ]);
    }

    /**
     * @inheritDoc
     */
    public function isVisible(int $characterID): bool
    {
        return true;
    }
}
