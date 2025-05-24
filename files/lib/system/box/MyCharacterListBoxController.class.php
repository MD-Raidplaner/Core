<?php

namespace rp\system\box;

use rp\system\cache\tolerant\MyCharactersCache;
use wcf\system\box\AbstractBoxController;
use wcf\system\WCF;

/**
 * Box controller for a list of my characters.
 *
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class MyCharacterListBoxController extends AbstractBoxController
{
    protected static $supportedPositions = [
        'sidebarLeft',
        'sidebarRight',
    ];

    #[\Override]
    protected function loadContent(): void
    {
        if (!WCF::getUser()->userID) {
            return;
        }

        $characters = (new MyCharactersCache(WCF::getUser()->userID))->getCache();
        if (empty($characters)) {
            return;
        }

        $this->content = WCF::getTPL()->render('rp', 'boxMyCharacterList',  [
            'boxCharacters' => $characters,
        ]);
    }
}
