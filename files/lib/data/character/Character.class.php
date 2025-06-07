<?php

namespace rp\data\character;

use rp\data\character\avatar\CharacterAvatar;
use rp\system\cache\runtime\CharacterProfileRuntimeCache;
use wcf\data\DatabaseObject;
use wcf\data\IPopoverObject;
use wcf\system\request\IRouteController;
use wcf\system\request\LinkHandler;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;

/**
 * Represents a character.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @property-read   int $characterID        unique id of the game
 * @property-read   string  $characterName      name of the character
 * @property-read   int|null    $userID     id of the user who created the character, or `null` if not already assigned.
 * @property-read   string $game        game identifier of the character
 * @property-read   int|null $avatarID      
 * @property-read   int $created        timestamp at which the character has been created
 * @property-read   int $lastUpdateTime     timestamp at which the character has been updated the last time
 * @property-read   string  $notes      notes of the character
 * @property-read   mixed[] $additionalData     array of additional data, which can be used to store arbitrary data
 * @property-read   string  $guildName       guild name if character does not belong to this guild
 * @property-read   int $views      number of times the character's profile has been visited
 * @property-read   int $isPrimary      is `1` if the character is primary character of this game, otherwise `0`
 * @property-read   int $isDisabled     is `1` if the character is disabled and thus is not displayed, otherwise `0`
 */
final class Character extends DatabaseObject implements IPopoverObject, IRouteController
{
    protected static $databaseTableName = 'member';

    /**
     * Returns true if the active user can delete this character.
     */
    public function canDelete(): bool
    {
        if (WCF::getSession()->getPermission('admin.rp.canDeleteCharacter')) {
            return true;
        }

        if ($this->userID == WCF::getUser()->userID && WCF::getSession()->getPermission('user.rp.canDeleteOwnCharacter')) {
            $characters = self::getAllCharactersByUserID($this->userID);
            if (\count($characters) == 1) return true;
            elseif (!$this->isPrimary) return true;

            return false;
        }

        return false;
    }

    /**
     * Returns true if the active user can edit this character.
     */
    public function canEdit(): bool
    {
        if (WCF::getSession()->getPermission('admin.rp.canEditCharacter')) {
            return true;
        }

        if ($this->userID == WCF::getUser()->userID && WCF::getSession()->getPermission('user.rp.canEditOwnCharacter')) {
            return true;
        }

        return false;
    }

    /**
     * Returns all characters by user id.
     * 
     * @return CharacterProfile[]
     */
    public static function getAllCharactersByUserID(int $userID): array
    {
        $sql = "SELECT  *
                FROM    rp1_member
                WHERE   userID = ?
                    AND game = ?
                    AND isDisabled = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([
            $userID,
            \RP_CURRENT_GAME,
            0,
        ]);

        /** @var Character[] $characters */
        $characters = $statement->fetchObject(Character::class);

        $characterProfile = [];
        foreach ($characters as $character) {
            $characterProfile[$character->characterID] = new CharacterProfile($character);
        }

        return $characterProfile;
    }

    /**
     * Returns the absolute location of the icon file.
     *
     * @return string[]
     */
    public function getAvatarFileUploadFileLocations(): array
    {
        if ($this->avatarID) {
            $avatar = new CharacterAvatar($this->avatarID);
            return [$avatar->getLocation()];
        }

        return [];
    }

    /**
     * Returns the character with the given character name.
     */
    public static function getCharacterByCharacterName(string $name): Character
    {
        $sql = "SELECT  *
                FROM    rp1_member
                WHERE   characterName = ?
                    AND game = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([
            $name,
            \RP_CURRENT_GAME,
        ]);
        $row = $statement->fetchArray();
        if (!$row) $row = [];

        return new self(null, $row);
    }

    #[\Override]
    public function getLink(): string
    {
        return LinkHandler::getInstance()->getLink('Character', [
            'application' => 'rp',
            'forceFrontend' => true,
            'object' => $this
        ]);
    }

    #[\Override]
    public function getPopoverLinkClass()
    {
        return 'rpCharacterLink';
    }

    public function getPrimaryCharacter(): ?CharacterProfile
    {
        if ($this->isPrimary || !$this->userID) {
            return new CharacterProfile($this);
        } else {
            $characterPrimaries = UserStorageHandler::getInstance()->getField('characterPrimaries', $this->userID);

            // cache does not exist or is outdated
            if ($characterPrimaries === null) {
                $sql = "SELECT  game, characterID
                        FROM    rp_member
                        WHERE   userID = ?
                            AND isPrimary = ?";
                $statement = WCF::getDB()->prepare($sql);
                $statement->execute([$this->userID, 1]);
                $characterPrimaries = $statement->fetchMap('game', 'characterID');

                // update storage characterPrimaries
                UserStorageHandler::getInstance()->update(
                    $this->userID,
                    'characterPrimaries',
                    \serialize($characterPrimaries)
                );
            } else {
                $characterPrimaries = \unserialize($characterPrimaries);
            }

            return CharacterProfileRuntimeCache::getInstance()->getObject($characterPrimaries[$this->game]);
        }
    }

    #[\Override]
    public function getTitle(): string
    {
        return $this->characterName;
    }

    #[\Override]
    protected function handleData($data): void
    {
        parent::handleData($data);

        // unserialize additional data
        $this->data['additionalData'] = (empty($data['additionalData']) ? [] : @\unserialize($data['additionalData']));
    }

    #[\Override]
    public function __get($name): mixed
    {
        $value = parent::__get($name);

        // treat additional data as data variables if it is an array
        $value ??= $this->data['additionalData'][$name] ?? null;

        return $value;
    }
}
