<?php

namespace rp\system\cache\tolerant;

use rp\data\character\CharacterProfileList;
use rp\data\point\account\PointAccountCache;
use wcf\system\cache\tolerant\AbstractTolerantCache;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\WCF;

/**
 * Caches the point information for characters for a specific user and game.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class CharacterPointCache extends AbstractTolerantCache
{
    public function __construct(
        private readonly int $userID,
        private readonly int $gameID = \RP_CURRENT_GAME_ID
    ) {}

    /**
     * Provides the default data structure for point information.
     * 
     * @return array{
     *   received: array{color: string, points: int},
     *   issued: array{color: string, points: int},
     *   adjustments: array{color: string, points: int},
     *   current: array{color: string, points: int}
     * }
     */
    protected function getDefaultData(): array
    {
        return [
            'received' => [
                'color' => '',
                'points' => 0
            ],
            'issued' => [
                'color' => '',
                'points' => 0
            ],
            'adjustments' => [
                'color' => '',
                'points' => 0
            ],
            'current' => [
                'color' => '',
                'points' => 0
            ],
        ];
    }

    #[\Override]
    public function getLifetime(): int
    {
        return 3_600;
    }

    /**
     * Fetches the total points issued for each character from the item-to-raid table.
     * 
     * @param int[] $characterIDs
     * @return array<int, array<int, int>> characterID => [pointAccountID => points]
     */
    private function fetchCharacterItems(array $characterIDs): array
    {
        $conditionBuilder = new PreparedStatementConditionBuilder();
        $conditionBuilder->add('characterID IN (?)', [$characterIDs]);

        $sql = "SELECT      characterID, pointAccountID, SUM(points) as points
                FROM        rp1_item_to_raid
                " . $conditionBuilder . "
                GROUP BY    characterID, pointAccountID";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute($conditionBuilder->getParameters());

        $characterItems = [];
        while ($row = $statement->fetchArray()) {
            $characterItems[$row['characterID']][$row['pointAccountID']] = $row['points'];
        }

        return $characterItems;
    }

    /**
     * Fetches the total raid points earned by each character from the raid and raid event tables.
     * 
     * @param int[] $characterIDs
     * @return array<int, array<int, int>> characterID => [pointAccountID => points]
     */
    private function fetchCharacterRaidPoints(array $characterIDs): array
    {
        $conditionBuilder = new PreparedStatementConditionBuilder();
        $conditionBuilder->add('attendee.characterID IN (?)', [$characterIDs]);

        $sql = "SELECT      SUM(raid.points) as points, raid_event.pointAccountID, attendee.characterID
                FROM        rp1_raid_attendee attendee
                LEFT JOIN   rp1_raid raid ON raid.raidID = attendee.raidID
                LEFT JOIN   rp1_raid_event raid_event ON raid.raidEventID = raid_event.eventID
                " . $conditionBuilder . "
                GROUP BY    attendee.characterID, raid_event.pointAccountID";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute($conditionBuilder->getParameters());

        $characterPoints = [];
        while ($row = $statement->fetchArray()) {
            $characterPoints[$row['characterID']][$row['pointAccountID']] = $row['points'];
        }

        return $characterPoints;
    }

    /**
     * @return array<int, array<int, array{
     *     received: array{color: string, points: int},
     *     issued: array{color: string, points: int},
     *     adjustments: array{color: string, points: int},
     *     current: array{color: string, points: int}
     * }>>
     */
    #[\Override]
    protected function rebuildCacheData(): array
    {
        $data = [];

        $pointAccounts = PointAccountCache::getInstance()->getAccounts();

        $characterList = new CharacterProfileList();
        $characterList->getConditionBuilder()->add('gameID = ?', [$this->gameID]);
        $characterList->getConditionBuilder()->add('userID = ?', [$this->userID]);
        $characterList->readObjects();
        $characters = $characterList->getObjects();
        $characterIDs = \array_keys($characters);

        // Fetch character item points
        $characterItems = $this->fetchCharacterItems($characterIDs);

        // Fetch character raid points
        $characterPoints = $this->fetchCharacterRaidPoints($characterIDs);

        /** @var CharacterProfile $character */
        foreach ($characters as $characterID => $character) {
            $data[$characterID] = [];

            /** @var PointAccount $pointAccount */
            foreach ($pointAccounts as $pointAccountID => $pointAccount) {
                $defaultData = $this->getDefaultData();
                $receivedPoints = $characterPoints[$characterID][$pointAccountID] ?? 0;
                $issuedPoints = $characterItems[$characterID][$pointAccountID] ?? 0;
                $currentPoints = $receivedPoints - $issuedPoints;

                $data[$characterID][$pointAccountID] = \array_merge($defaultData, [
                    'received' => [
                        'color' => $receivedPoints > 0 ? 'green' : '',
                        'points' => $receivedPoints,
                    ],
                    'issued' => [
                        'color' => $issuedPoints > 0 ? 'red' : '',
                        'points' => $issuedPoints,
                    ],
                    'current' => [
                        'color' => $currentPoints < 0 ? 'red' : ($currentPoints > 0 ? 'green' : ''),
                        'points' => $currentPoints,
                    ],
                ]);
            }
        }

        return $data;
    }
}
