<?php

namespace rp\data\character;

use rp\data\ICharacterContent;
use wcf\data\DatabaseObject;
use wcf\data\IPopoverObject;
use wcf\system\request\IRouteController;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Represents a character.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @property-read int $characterID      unique id of the character
 * @property-read string $characterName    name of the character
 * @property-read ?int $userID           user id of the character owner or null for guest characters
 * @property-read string $username        username of the character owner
 * @property-read string $game            game identifier of the character
 * @property-read ?int $avatarFileID     file id of the character avatar or null if not set
 * @property-read int $created         timestamp of character creation
 * @property-read int $lastUpdateTime timestamp of the last character update
 * @property-read string $guildName       name of the character's guild
 * @property-read int $views           number of views of the character profile
 * @property-read bool $isPrimary       whether this character is marked as primary character
 * @property-read bool $isDisabled      whether this character is disabled
 * @property-read string $notes           notes for the character
 * @property-read mixed[] $additionalData  additional data for the character
 */
class Character extends DatabaseObject implements IPopoverObject, IRouteController, ICharacterContent
{
    /**
     * Returns true if the current user can delete this character.
     */
    public function canDelete(): bool
    {
        if (WCF::getSession()->getPermission('admin.rp.canDeleteCharacter')) {
            return true;
        }

        if ($this->userID === WCF::getUser()->getObjectID() && WCF::getSession()->getPermission('user.rp.canDeleteOwnCharacter')) {
            $characters = self::getCharactersByUserID($this->userID);
            return \count($characters) === 1 || !$this->isPrimary;
        }

        return false;
    }

    /**
     * Returns true if the current user can edit this character.
     */
    public function canEdit(): bool
    {
        if (WCF::getSession()->getPermission('admin.rp.canEditCharacter')) {
            return true;
        }

        return $this->userID === WCF::getUser()->getObjectID() && WCF::getSession()->getPermission('user.rp.canEditOwnCharacter');
    }

    /**
     * Returns the character with the given name or null if no such character exists.
     */
    public static function getCharacterByName(string $characterName): Character
    {
        $sql = "SELECT  character_table.*
                FROM    rp1_character character_table
                WHERE   character_table.characterName = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$characterName]);
        $row = $statement->fetchArray();
        if (!$row) {
            $row = [];
        }

        return new self(null, $row);
    }

    /**
     * Returns all characters of the given user.
     * 
     * @return array<int, Character>
     */
    public static function getCharactersByUserID(int $userID): array
    {
        $characterList = new CharacterList();
        $characterList->getConditionBuilder()->add('character_table.userID = ?', [$userID]);
        $characterList->getConditionBuilder()->add('character_table.isDisabled = ?', [0]);
        $characterList->readObjects();

        return $characterList->getObjects();
    }

    #[\Override]
    public function getCharacterID(): int
    {
        return $this->getObjectID();
    }

    #[\Override]
    public function getCharacterName(): string
    {
        return $this->characterName;
    }

    #[\Override]
    public static function getDatabaseTableAlias(): string
    {
        return 'character_table';
    }

    #[\Override]
    public function getLink(): string
    {
        return LinkHandler::getInstance()->getLink('Character', [
            'application' => 'rp',
            'forceFrontend' => true,
            'object' => $this,
        ]);
    }

    #[\Override]
    public function getPopoverLinkClass(): string
    {
        return 'characterLink';
    }

    #[\Override]
    public function getTime(): int
    {
        return $this->created;
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
        $this->data['additionalData'] = @\unserialize($data['additionalData']);
        if (!\is_array($this->data['additionalData'])) {
            $this->data['additionalData'] = [];
        }
    }
}
