<?php

namespace rp\system\box;

use rp\system\cache\builder\MyCharactersCacheBuilder;
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
        if (!WCF::getUser()->userID)  return;

        $characters = MyCharactersCacheBuilder::getInstance()->getData(
            [
                'userID' => WCF::getUser()->userID
            ]
        );
        $characters = $characters[RP_CURRENT_GAME_ID] ?? [];

        if (empty($characters)) return;

        $this->content = WCF::getTPL()->fetch('boxMyCharacterList', 'rp', [
            'boxCharacters' => $characters,
        ], true);
    }
}
