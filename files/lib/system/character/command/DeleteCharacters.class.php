<?php

namespace rp\system\character\command;

use rp\data\character\Character;
use rp\data\character\CharacterEditor;
use rp\event\character\CharactersDeleted;
use wcf\data\file\FileAction;
use wcf\system\user\storage\UserStorageHandler;

/**
 * Deletes a bunch of characters.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @property-read Character[] $characters
 */
final class DeleteCharacters
{
    public function __construct(
        private readonly array $characters,
    ) {}

    public function __invoke(): void
    {
        $avatarFileIDs = $userIDs = [];
        foreach ($this->characters as $character) {
            if ($character->avatarFileID !== null) {
                $avatarFileIDs[] = $character->avatarFileID;
            }

            if ($character->userID) {
                $userIDs[] = $character->userID;
            }
        }

        if (!empty($avatarFileIDs)) {
            (new FileAction($avatarFileIDs, 'delete'))->executeAction();
        }

        if (!empty($userIDs)) {
            UserStorageHandler::getInstance()->reset($userIDs, 'characterPrimaryIDs');
        }

        CharacterEditor::deleteAll(\array_column($this->characters, 'characterID'));

        $event = new CharactersDeleted($this->characters);
        EventHandler::getInstance()->fire($event);
    }
}
