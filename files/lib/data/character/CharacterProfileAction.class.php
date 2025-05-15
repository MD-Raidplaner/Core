<?php

namespace rp\data\character;

use rp\data\character\avatar\CharacterAvatar;
use rp\data\character\avatar\CharacterAvatarAction;
use rp\data\character\avatar\CharacterAvatarEditor;
use wcf\system\user\storage\UserStorageHandler;
use wcf\util\FileUtil;
use wcf\util\ImageUtil;

/**
 * Executes character profile-related actions.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
class CharacterProfileAction extends CharacterAction
{
    /**
     * Sets an avatar for a given character. The given file will be renamed and is gone after this method call.
     *
     * @throws  UserInputException      If none or more than one character is given.
     * @throws  \InvalidArgumentException       If the given file is not an image or is incorrectly sized.
     */
    public function setAvatar(): array
    {
        $character = $this->getSingleObject();

        $imageData = \getimagesize($this->parameters['fileLocation']);
        if (!$imageData) {
            throw new \InvalidArgumentException("The given file is not an image.");
        }

        if (
            ($imageData[0] != CharacterAvatar::AVATAR_SIZE || $imageData[1] != CharacterAvatar::AVATAR_SIZE)
            && ($imageData[0] != CharacterAvatar::AVATAR_SIZE_2X || $imageData[1] != CharacterAvatar::AVATAR_SIZE_2X)
        ) {
            throw new \InvalidArgumentException(
                \sprintf(
                    "The given file does not have the size of %dx%d",
                    CharacterAvatar::AVATAR_SIZE,
                    CharacterAvatar::AVATAR_SIZE
                )
            );
        }

        $data = [
            'avatarExtension' => ImageUtil::getExtensionByMimeType($imageData['mime']),
            'avatarName' => $this->parameters['filename'] ?? \basename($this->parameters['fileLocation']),
            'characterID' => $character->characterID,
            'fileHash' => \sha1_file($this->parameters['fileLocation']),
            'height' => $imageData[1],
            'width' => $imageData[0],
        ];

        // create avatar
        $avatar = CharacterAvatarEditor::create($data);

        try {
            $dir = \dirname($avatar->getLocation(null, false));
            if (!\file_exists($dir)) {
                FileUtil::makePath($dir);
            }

            \rename($this->parameters['fileLocation'], $avatar->getLocation(null, false));

            $avatarEditor = new CharacterAvatarEditor($avatar);
            if ($avatarEditor->createAvatarVariant()) {
                $avatar = new CharacterAvatar($avatar->avatarID);
            }

            $characterEditor = new CharacterEditor($character->getDecoratedObject());
            $characterEditor->update([
                'avatarID' => $avatar->avatarID,
            ]);
        } catch (\Exception $e) {
            $editor = new CharacterAvatarEditor($avatar);
            $editor->delete();
        }

        // delete old avatar
        if ($character->avatarID) {
            (new CharacterAvatarAction([$character->avatarID], 'delete'))->executeAction();
        }

        if ($character->userID) {
            // reset user storage
            UserStorageHandler::getInstance()->reset([$character->userID], 'characterAvatars');
        }

        return [
            'avatar' => $avatar,
        ];
    }
}
