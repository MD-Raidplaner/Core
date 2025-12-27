<?php

namespace rp\command\character;

use rp\data\character\AccessibleCharacterList;
use rp\data\character\Character;
use rp\data\character\CharacterEditor;
use rp\data\character\CharacterList;
use rp\event\character\CharacterCreated;
use rp\system\character\CharacterHandler;
use wcf\system\cache\runtime\UserRuntimeCache;
use wcf\system\event\EventHandler;
use wcf\system\request\RequestHandler;
use wcf\system\user\storage\UserStorageHandler;

/**
 * Creates a new character.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
final class CreateCharacter
{
    /**
     * @param array<string,mixed> $data
     * @param mixed[] $formData
     */
    public function __construct(
        private readonly array $data,
        private readonly array $formData
    ) {}

    public function __invoke(): Character
    {
        $data = $this->data;

        $data['created'] = \TIME_NOW;

        if ($data['userID'] !== null) {
            if (RequestHandler::getInstance()->isACPRequest()) {
                $characterList = new CharacterList();
                $characterList->getConditionBuilder()->add('character_table.userID = ?', [$data['userID']]);
                $characterList->getConditionBuilder()->add('character_table.isPrimary = ?', [1]);
                $data['isPrimary'] = $characterList->countObjects() === 0 ? 1 : 0;
            } else {
                $data['isPrimary'] = CharacterHandler::getInstance()->getPrimaryCharacter() === null ? 1 : 0;
            }

            $user = UserRuntimeCache::getInstance()->getObject($data['userID']);
            $data['username'] = $user->username;
        } else {
            $data['isPrimary'] = 1;
            $data['isDisabled'] = 1;
        }

        $character = CharacterEditor::createOrIgnore($data);
        \assert($character instanceof Character);

        EventHandler::getInstance()->fire(
            new CharacterCreated($character, $this->formData)
        );

        if ($character->userID) {
            UserStorageHandler::getInstance()->reset([$character->userID], 'characterPrimaryIDs');
        }

        return $character;
    }
}
