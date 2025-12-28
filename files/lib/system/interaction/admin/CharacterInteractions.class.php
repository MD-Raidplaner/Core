<?php

namespace rp\system\interaction\admin;

use rp\data\character\Character;
use rp\data\character\CharacterProfile;
use rp\event\interaction\admin\CharacterInteractionCollecting;
use wcf\system\event\EventHandler;
use wcf\system\interaction\AbstractInteractionProvider;
use wcf\system\interaction\DeleteInteraction;
use wcf\system\interaction\InteractionEffect;
use wcf\system\interaction\LinkableObjectInteraction;
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
            new LinkableObjectInteraction('view', 'rp.acp.character.button.viewCharacter'),
            new DeleteInteraction('rp/core/characters/%s', static function (CharacterProfile $character): bool {
                return $character->canDelete();
            }),
            new RpcInteraction(
                'setPrimary',
                'rp/core/characters/%s/setPrimary',
                'rp.character.button.setPrimary',
                isAvailableCallback: static function (CharacterProfile $character): bool {
                    return !$character->isPrimary;
                },
                interactionEffect: InteractionEffect::ReloadList,
            ),
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
