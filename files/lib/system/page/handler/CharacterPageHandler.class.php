<?php

namespace rp\system\page\handler;

use wcf\system\cache\runtime\UserRuntimeCache;
use wcf\system\page\handler\AbstractLookupPageHandler;
use wcf\system\page\handler\IOnlineLocationPageHandler;

/**
 * Menu page handler for the character profile page.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class CharacterPageHandler extends AbstractLookupPageHandler implements IOnlineLocationPageHandler
{
    use TCharacterLookupPageHandler;
    use TCharacterOnlineLocationPageHandler;

    /**
     * @inheritDoc
     */
    public function getLink($objectID): string
    {
        return UserRuntimeCache::getInstance()->getObject($objectID)->getLink();
    }
}
