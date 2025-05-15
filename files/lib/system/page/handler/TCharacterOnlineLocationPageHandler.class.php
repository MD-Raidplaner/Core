<?php

namespace rp\system\page\handler;

use rp\system\cache\runtime\CharacterRuntimeCache;
use wcf\data\page\Page;
use wcf\data\user\online\UserOnline;
use wcf\system\page\handler\IOnlineLocationPageHandler;
use wcf\system\page\handler\TOnlineLocationPageHandler;
use wcf\system\WCF;

/**
 * Implementation of the `IOnlineLocationPageHandler` interface for character-bound pages.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
trait TCharacterOnlineLocationPageHandler
{
    use TOnlineLocationPageHandler;

    /**
     * Returns the textual description if a user is currently online viewing this page.
     *
     * @see IOnlineLocationPageHandler::getOnlineLocation()
     */
    public function getOnlineLocation(Page $page, UserOnline $user): string
    {
        if ($user->pageObjectID === null) {
            return '';
        }

        $characterObject = CharacterRuntimeCache::getInstance()->getObject($user->pageObjectID);
        if ($characterObject === null) {
            return '';
        }

        return WCF::getLanguage()->getDynamicVariable('wcf.page.onlineLocation.' . $page->identifier, [
            'character' => $characterObject,
            'userOnline' => $user,
        ]);
    }

    /**
     * Prepares fetching all necessary data for the textual description if a user is currently online
     * viewing this page.
     *
     * @see IOnlineLocationPageHandler::prepareOnlineLocation()
     */
    public function prepareOnlineLocation(Page $page, UserOnline $user): void
    {
        if ($user->pageObjectID !== null) {
            CharacterRuntimeCache::getInstance()->cacheObjectID($user->pageObjectID);
        }
    }
}
