<?php

namespace rp\system\interaction\user;

use rp\data\character\Character;
use rp\data\character\CharacterProfile;
use rp\event\interaction\user\CharacterInteractionCollecting;
use rp\form\CharacterEditForm;
use wcf\system\event\EventHandler;
use wcf\system\interaction\AbstractInteractionProvider;
use wcf\system\interaction\DeleteInteraction;
use wcf\system\interaction\Divider;
use wcf\system\interaction\EditInteraction;
use wcf\system\interaction\RpcInteraction;

/**
 * Interaction provider for characters.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
final class CharacterInteractions extends AbstractInteractionProvider
{
    public function __construct()
    {
        $this->addInteractions([
            new DeleteInteraction('rp/characters/%s', function (CharacterProfile $character) {
                return $character->canDelete();
            }),
            new RpcInteraction(
                'setPrimary',
                'rp/characters/%s/setPrimary',
                'rp.character.button.setPrimary',
                isAvailableCallback: static function (CharacterProfile $character): bool {
                    return $character->isPrimary === 0;
                },
                invalidatesAllItems: true,
            ),
            new Divider(),
            new EditInteraction(CharacterEditForm::class, function (CharacterProfile $character) {
                return $character->canEdit();
            }),
        ]);

        EventHandler::getInstance()->fire(
            new CharacterInteractionCollecting($this)
        );
    }

    #[\Override]
    public function getObjectClassName(): string
    {
        return Character::class;
    }
}
