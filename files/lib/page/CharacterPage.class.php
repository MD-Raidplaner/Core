<?php

namespace rp\page;

use CuyZ\Valinor\Mapper\MappingError;
use rp\data\character\CharacterEditor;
use rp\data\character\CharacterProfile;
use rp\event\character\profile\menu\CharacterProfileMenuCollecting;
use rp\system\cache\runtime\CharacterProfileRuntimeCache;
use wcf\http\Helper;
use wcf\page\AbstractPage;
use wcf\system\event\EventHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\MetaTagHandler;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Shows the character profile page.
 *
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
final class CharacterPage extends AbstractPage
{
    /**
     * character id
     */
    public int $characterID = 0;

    /**
     * character object
     */
    public ?CharacterProfile $character;

    /**
     * character menus
     */
    private iterable $menus;

    /**
     * @inheritDoc
     */
    public function assignVariables(): void
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'character' => $this->character,
            'characterID' => $this->characterID,
            'menus' => \iterator_to_array($this->menus),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function readData(): void
    {
        parent::readData();

        // update profile hits
        if ($this->character->userID != WCF::getUser()->userID && !WCF::getSession()->spiderID) {
            $editor = new CharacterEditor($this->character->getDecoratedObject());
            $editor->updateCounters(['views' => 1]);
        }

        // get character menus
        $event = new CharacterProfileMenuCollecting();
        EventHandler::getInstance()->fire($event);

        $this->menus = $event->getMenus();

        MetaTagHandler::getInstance()->addTag(
            'og:url',
            'og:url',
            LinkHandler::getInstance()->getLink(
                'Character',
                [
                    'application' => 'rp',
                    'object' => $this->character->getDecoratedObject()
                ]
            ),
            true
        );
        MetaTagHandler::getInstance()->addTag('og:type', 'og:type', 'profile', true);
        MetaTagHandler::getInstance()->addTag('profile:username', 'profile:username', $this->character->characterName, true);
        MetaTagHandler::getInstance()->addTag(
            'og:title',
            'og:title',
            $this->character->characterName . ' - ' . WCF::getLanguage()->get('rp.character.characters') . ' - ' . WCF::getLanguage()->get(\PAGE_TITLE),
            true
        );
        MetaTagHandler::getInstance()->addTag('og:image', 'og:image', $this->character->getAvatar()->getURL(), true);
    }

    /**
     * @inheritDoc
     */
    public function readParameters(): void
    {
        try {
            $parameters = Helper::mapQueryParameters(
                $_GET,
                <<<'EOT'
                array {
                    id?: positive-int
                }
                EOT
            );
            $this->characterID = $parameters['id'] ?? 0;
        } catch (MappingError) {
            throw new IllegalLinkException();
        }

        $this->character = CharacterProfileRuntimeCache::getInstance()->getObject($this->characterID);
        if ($this->character === null) {
            throw new IllegalLinkException();
        }

        if (
            $this->character->userID != WCF::getUser()->userID
            && !WCF::getSession()->getPermission('user.rp.canViewCharacterProfile')
        ) {
            throw new PermissionDeniedException();
        }

        $this->canonicalURL = $this->character->getLink();
    }
}
