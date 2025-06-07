<?php

namespace rp\data\character;

use PDOException;
use rp\data\character\avatar\CharacterAvatarDecorator;
use rp\data\character\avatar\DefaultCharacterAvatar;
use rp\system\game\GameHandler;
use rp\system\game\GameItem;
use wcf\data\DatabaseObjectDecorator;
use wcf\data\ITitledLinkObject;
use wcf\system\cache\runtime\FileRuntimeCache;
use wcf\system\database\exception\DatabaseQueryException;
use wcf\system\database\exception\DatabaseQueryExecutionException;
use wcf\system\exception\SystemException;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;

/**
 * Decorates the character object and provides functions to retrieve data for character profiles.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @mixin   Character
 * @extends DatabaseObjectDecorator<Character>
 */
final class CharacterProfile extends DatabaseObjectDecorator implements ITitledLinkObject
{
    /**
     * character avatar
     */
    protected ?CharacterAvatarDecorator $avatar = null;
    protected static $baseClass = Character::class;

    /**
     * Returns true if the active user can edit the avatar of this character.
     */
    public function canEditAvatar(): bool
    {
        if (WCF::getSession()->getPermission('admin.rp.canEditCharacter')) {
            return true;
        }

        if ($this->userID !== WCF::getUser()->userID) {
            return false;
        }


        return WCF::getSession()->getPermission('user.rp.canUploadCharacterAvatar')
            && WCF::getSession()->getPermission('user.rp.canEditOwnCharacter');
    }

    /**
     * Returns the character's avatar.
     */
    public function getAvatar(): CharacterAvatarDecorator
    {
        if ($this->avatar === null) {
            $avatar = null;

            if ($this->avatarFileID) {
                $avatars = [];

                if ($this->userID) {
                    $data = UserStorageHandler::getInstance()->getField('characterAvatars', $this->userID);
                    if ($data !== null) {
                        $avatars = \unserialize($data);
                    }

                    if (isset($avatars[$this->characterID])) {
                        $avatar = $avatars[$this->characterID];
                    } else {
                        $avatar = FileRuntimeCache::getInstance()->getObject($this->avatarFileID);

                        $avatars[$this->characterID] = $avatar;
                        UserStorageHandler::getInstance()->update(
                            $this->userID,
                            'characterAvatars',
                            \serialize($avatars)
                        );
                    }
                } else {
                    $avatar = FileRuntimeCache::getInstance()->getObject($this->avatarFileID);
                }
            }

            // use default avatar
            if ($avatar === null) {
                $avatar = new DefaultCharacterAvatar($this->characterName ?: '');
            }

            $this->avatar = new CharacterAvatarDecorator($avatar);
        }

        return $this->avatar;
    }

    /**
     * Returns the character profile with the given character name.
     */
    public static function getCharacterProfileByCharacterName(string $name): CharacterProfile
    {
        $character = Character::getCharacterByCharacterName($name);
        return new self($character);
    }

    /**
     * Returns game object of this character.
     */
    public function getGame(): GameItem
    {
        return GameHandler::getInstance()->getGameByIdentifier($this->game);
    }

    #[\Override]
    public function getLink(): string
    {
        return $this->getDecoratedObject()->getLink();
    }

    #[\Override]
    public function getTitle(): string
    {
        return $this->getDecoratedObject()->getTitle();
    }

    /**
     * Sets the avatar for this character.
     */
    public function setFileAvatar(File $file): void
    {
        $this->avatar = new CharacterAvatarDecorator($file);
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->getTitle();
    }
}
