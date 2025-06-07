<?php

namespace rp\system\character\command;

use rp\data\character\Character;
use rp\data\character\CharacterEditor;
use rp\system\cache\runtime\CharacterProfileRuntimeCache;
use wcf\data\file\File;
use wcf\data\file\FileAction;
use wcf\system\user\storage\UserStorageHandler;

/**
 * Sets the avatar of a character.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class SetAvatar
{
    public function __construct(
        private readonly Character $character,
        private readonly ?File $file = null
    ) {}

    public function __invoke(): void
    {
        if ($this->file === null && $this->character->avatarFileID !== null) {
            (new FileAction([$this->character->avatarFileID], 'delete'))->executeAction();
        }

        (new CharacterEditor($this->character))->update([
            'avatarFileID' => $this->file?->fileID,
        ]);

        if ($this->character->userID) {
            UserStorageHandler::getInstance()->reset([$this->character->userID], 'characterAvatars');
        }
        CharacterProfileRuntimeCache::getInstance()->removeObject($this->character->getObjectID());
    }
}
