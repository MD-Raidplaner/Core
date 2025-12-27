<?php

namespace rp\data\character;

use rp\system\cache\runtime\CharacterProfileRuntimeCache;
use wcf\data\DatabaseObjectDecorator;
use wcf\data\ITitledLinkObject;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;

/**
 * Decorates the character object and provides functionality for character profiles.
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
    protected static $baseClass = Character::class;

    #[\Override]
    public function getLink(): string
    {
        return $this->getDecoratedObject()->getLink();
    }

    /**
     * Returns the primary character of the user owning this character.
     */
    public function getPrimaryCharacter(): ?CharacterProfile
    {
        if ($this->isPrimary || !$this->userID) {
            return $this;
        } else {
            $characterPrimaries = UserStorageHandler::getInstance()->getField('characterPrimaries', $this->userID);

            // cache does not exist or is outdated
            if ($characterPrimaries === null) {
                $sql = "SELECT  game, characterID
                        FROM    rp_character
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
        return $this->getDecoratedObject()->getTitle();
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->getTitle();
    }
}
