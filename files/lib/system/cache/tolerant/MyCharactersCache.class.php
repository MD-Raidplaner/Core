<?php

namespace rp\system\cache\tolerant;

use PDOException;
use rp\data\character\CharacterProfile;
use rp\data\character\CharacterProfileList;
use wcf\system\cache\tolerant\AbstractTolerantCache;
use wcf\system\database\exception\DatabaseQueryException;
use wcf\system\database\exception\DatabaseQueryExecutionException;
use wcf\system\exception\SystemException;

/**
 * Caches the character profiles for a specific user and game.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @extends AbstractTolerantCache<array<int, CharacterProfile>>
 */
final class MyCharactersCache extends AbstractTolerantCache
{
    public function __construct(
        private readonly int $userID,
        private readonly string $game = \RP_CURRENT_GAME
    ) {}

    #[\Override]
    public function getLifetime(): int
    {
        return 300;
    }

    /**
     * @return array<int, CharacterProfile>
     */
    #[\Override]
    protected function rebuildCacheData(): array
    {
        $characterList = new CharacterProfileList();
        $characterList->getConditionBuilder()->add('userID = ?', [$this->userID]);
        $characterList->getConditionBuilder()->add('game = ?', [$this->game]);
        $characterList->getConditionBuilder()->add('isDisabled = ?', [0]);
        $characterList->sqlOrderBy = 'characterName ASC';
        $characterList->readObjects();

        return $characterList->getObjects();
    }
}
