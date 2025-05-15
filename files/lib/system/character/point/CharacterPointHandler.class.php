<?php

namespace rp\system\character\point;

use rp\data\character\CharacterProfile;
use rp\system\cache\builder\CharacterPointCacheBuilder;
use rp\util\RPUtil;
use wcf\system\SingletonFactory;

/**
 * Handles character points.
 * 
 * Manages the points associated with characters, including primary and secondary (twink) characters.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class CharacterPointHandler extends SingletonFactory
{
    /**
     * Cached character points indexed by character ID
     */
    protected array $characterPoints = [];

    /**
     * Accumulates the point data into the existing data.
     */
    private function accumulatePointData(array &$existingData, array $newData): void
    {
        $existingData['received']['points'] += $newData['received']['points'];
        $existingData['received']['color'] = $existingData['received']['points'] > 0 ? 'green' : '';

        $existingData['adjustments']['points'] += $newData['adjustments']['points'];
        $existingData['adjustments']['color'] = $existingData['adjustments']['points'] > 0 ? 'red' : '';

        $existingData['issued']['points'] += $newData['issued']['points'];
        $existingData['issued']['color'] = $newData['issued']['points'] > 0 ? 'red' : '';

        $currentPoints = $existingData['received']['points'] - $existingData['issued']['points'];
        $existingData['current']['points'] = $currentPoints;
        $existingData['current']['color'] = $currentPoints < 0 ? 'red' : ($currentPoints > 0 ? 'green' : '');
    }

    /**
     * Aggregates points data for characters if twinks are not shown.
     */
    private function aggregatePoints(array $points): array
    {
        $aggregatedData = [];

        foreach ($points as $pointAccounts) {
            foreach ($pointAccounts as $pointAccountID => $data) {
                if (!isset($aggregatedData[$pointAccountID])) {
                    $aggregatedData[$pointAccountID] = $data;
                } else {
                    $this->accumulatePointData($aggregatedData[$pointAccountID], $data);
                }
            }
        }

        return $aggregatedData;
    }

    /**
     * Formats the points data for display.
     */
    private function formatPointsData(): void
    {
        foreach ($this->characterPoints as &$userData) {
            foreach ($userData as  &$pointAccountData) {
                $pointAccountData['received']['points'] = RPUtil::formatPoints($pointAccountData['received']['points']);
                $pointAccountData['adjustments']['points'] = RPUtil::formatPoints($pointAccountData['adjustments']['points']);
                $pointAccountData['issued']['points'] = RPUtil::formatPoints($pointAccountData['issued']['points']);
                $pointAccountData['current']['points'] = RPUtil::formatPoints($pointAccountData['current']['points']);
            }
        }
    }

    /**
     * Retrieves the points for the given character.
     */
    public function getPoints(CharacterProfile $character): array
    {
        $primaryCharacter = $character->getPrimaryCharacter();
        $this->loadCharacterPoints($primaryCharacter);

        $characterID = $character->getObjectID();
        $primaryID = $primaryCharacter->getObjectID();

        return RP_SHOW_TWINKS
            ? ($this->characterPoints[$characterID] ?? [])
            : ($this->characterPoints[$primaryID] ?? []);
    }

    /**
     * Loads the character points from the cache if not already loaded.
     */
    private function loadCharacterPoints(CharacterProfile $primaryCharacter): void
    {
        $primaryID = $primaryCharacter->getObjectID();

        if (isset($this->characterPoints[$primaryID])) {
            return;
        }

        $points = CharacterPointCacheBuilder::getInstance()->getData([
            'gameID' => RP_CURRENT_GAME_ID,
            'userID' => $primaryCharacter->userID
        ]);

        if (RP_SHOW_TWINKS) {
            $this->characterPoints = $points;
        } else {
            $this->characterPoints[$primaryID] = $this->aggregatePoints($points);
        }

        $this->formatPointsData();
    }
}
