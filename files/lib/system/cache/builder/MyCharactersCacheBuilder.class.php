<?php

namespace rp\system\cache\builder;

use rp\data\character\Character;
use rp\data\character\CharacterProfile;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\WCF;

/**
 * Caches my characters.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class MyCharactersCacheBuilder extends AbstractCacheBuilder
{
    /**
     * @inheritDoc
     */
    protected $maxLifetime = 300;

    /**
     * @inheritDoc
     */
    protected function rebuild(array $parameters): array
    {
        $data = [];

        $sql = "SELECT      *
                FROM        rp1_member
                WHERE       userID = ?
                    AND     isDisabled = ?
                ORDER BY    characterName ASC";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([
            $parameters['userID'],
            0,
        ]);

        /** @var Character $object */
        while ($character = $statement->fetchObject(Character::class)) {
            $data[$character->gameID] ??= [];
            $data[$character->gameID][] = new CharacterProfile($character);
        }

        return $data;
    }
}
