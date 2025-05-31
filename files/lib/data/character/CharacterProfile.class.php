<?php

namespace rp\data\character;

use rp\data\character\avatar\CharacterAvatar;
use rp\data\character\avatar\CharacterAvatarDecorator;
use rp\data\character\avatar\DefaultCharacterAvatar;
use rp\system\game\GameHandler;
use wcf\data\DatabaseObjectDecorator;
use wcf\data\ITitledLinkObject;
use wcf\system\user\storage\UserStorageHandler;

/**
 * Decorates the character object and provides functions to retrieve data for character profiles.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class CharacterProfile extends DatabaseObjectDecorator implements ITitledLinkObject
{
    /**
     * character avatar
     */
    protected ?CharacterAvatarDecorator $avatar = null;
    protected static $baseClass = Character::class;

    /**
     * Returns the character's avatar.
     */
    public function getAvatar(): CharacterAvatarDecorator
    {
        if ($this->avatar === null) {
            $avatar = null;

            if ($this->avatarID) {
                if (!$this->fileHash) {
                    $avatars = [];

                    if ($this->userID) {
                        $data = UserStorageHandler::getInstance()->getField('characterAvatars', $this->userID);
                        if ($data !== null) {
                            $avatars = \unserialize($data);
                        }

                        if (isset($avatars[$this->characterID])) {
                            $avatar = $avatars[$this->characterID];
                        } else {
                            $avatar = new CharacterAvatar($this->avatarID);

                            $avatars[$this->characterID] = $avatar;
                            UserStorageHandler::getInstance()->update(
                                $this->userID,
                                'characterAvatars',
                                \serialize($avatars)
                            );
                        }
                    } else {
                        $avatar = new CharacterAvatar($this->avatarID);
                    }
                } else {
                    $avatar = new CharacterAvatar(null, $this->getDecoratedObject()->data);
                }
            }

            // use default avatar
            if ($avatar === null) {
                $avatar = new DefaultCharacterAvatar($this->characterName);
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
    public function getGame(): Game
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

    #[\Override]
    public function __toString(): string
    {
        return $this->getDecoratedObject()->__toString();
    }
}
