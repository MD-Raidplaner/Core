<?php

namespace rp\system\character\profile\menu;

use rp\data\point\account\PointAccount;
use rp\system\cache\builder\RaidStatsCacheBuilder;
use rp\system\cache\eager\PointAccountCache;
use rp\system\cache\runtime\CharacterRuntimeCache;
use rp\system\cache\tolerant\CharacterPointCache;
use rp\util\RPUtil;
use wcf\system\WCF;

/**
 * Character menu implementation for points content.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class PointsCharacterProfileMenu implements ICharacterProfileMenu
{
    #[\Override]
    public function getContent(int $characterID): string
    {
        $character = CharacterRuntimeCache::getInstance()->getObject($characterID);
        if ($character === null) return '';

        $primaryCharacter = $character->getPrimaryCharacter();
        $characterPoints = (new CharacterPointCache($primaryCharacter->userID))->getCache();

        if (!isset($characterPoints[$characterID])) return '';
        $characterPoints = $characterPoints[$characterID];

        $pointAccounts = (new PointAccountCache())->getCache()->getAccounts();
        $raidStats = RaidStatsCacheBuilder::getInstance()->getData(['gameID' => RP_CURRENT_GAME_ID]);

        $sql = "SELECT      raid.date, raidEvent.pointAccountID
                FROM        rp1_raid raid
                LEFT JOIN   rp1_raid_attendee attendee
                ON          raid.raidID = attendee.raidID
                LEFT JOIN   rp1_raid_event raidEvent
                ON          raid.raidEventID = raidEvent.eventID
                WHERE       attendee.characterID = ?
                    AND     raid.gameID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$characterID, RP_CURRENT_GAME_ID]);
        $raidDates = $statement->fetchMap('pointAccountID', 'date', false);

        $characterStats = [];
        foreach ($pointAccounts as $pointAccountID => $pointAccount) {
            $characterStats[$pointAccountID] = [
                'raid30' => ['color' => 'red', 'is' => 0, 'max' => $raidStats[$pointAccountID]['raid30'] ?? 0, 'percent' => 0],
                'raid60' => ['color' => 'red', 'is' => 0, 'max' => $raidStats[$pointAccountID]['raid60'] ?? 0, 'percent' => 0],
                'raid90' => ['color' => 'red', 'is' => 0, 'max' => $raidStats[$pointAccountID]['raid90'] ?? 0, 'percent' => 0],
                'raidAll' => ['color' => 'red', 'is' => 0, 'max' => $raidStats[$pointAccountID]['raidAll'] ?? 0, 'percent' => 0],
            ];

            if (isset($raidDates[$pointAccountID])) {
                foreach ($raidDates[$pointAccountID] as $date) {
                    $characterStats[$pointAccountID]['raidAll']['is']++;
                    if ($date >= (TIME_NOW - (90 * 86400))) $characterStats[$pointAccountID]['raid90']['is']++;
                    if ($date >= (TIME_NOW - (60 * 86400))) $characterStats[$pointAccountID]['raid60']['is']++;
                    if ($date >= (TIME_NOW - (30 * 86400))) $characterStats[$pointAccountID]['raid30']['is']++;
                }
            }

            foreach (['raid30', 'raid60', 'raid90', 'raidAll'] as $type) {
                if (
                    !$characterStats[$pointAccountID][$type]['is'] &&
                    !$characterStats[$pointAccountID][$type]['max']
                ) {
                    continue;
                }

                $characterStats[$pointAccountID][$type]['percent'] = \number_format(
                    ($characterStats[$pointAccountID][$type]['is'] /
                        $characterStats[$pointAccountID][$type]['max']) * 100
                );

                if (
                    $characterStats[$pointAccountID][$type]['percent'] >= 40 &&
                    $characterStats[$pointAccountID][$type]['percent'] < 80
                ) {
                    $characterStats[$pointAccountID][$type]['color'] = 'yellow';
                } else if ($characterStats[$pointAccountID][$type]['percent'] >= 80) {
                    $characterStats[$pointAccountID][$type]['color'] = 'green';
                }
            }

            foreach (['adjustments', 'current', 'issued', 'received'] as $type) {
                $characterPoints[$pointAccountID][$type]['points'] = RPUtil::formatPoints($characterPoints[$pointAccountID][$type]['points']);
            }
        }

        \usort($pointAccounts, static function (PointAccount $a, PointAccount $b) {
            return \strcasecmp($a->getTitle(), $b->getTitle());
        });

        return WCF::getTPL()->fetch('characterProfilePoints', 'rp', [
            'characterPoints' => $characterPoints,
            'characterStats' => $characterStats,
            'pointAccounts' => $pointAccounts,
        ]);
    }

    #[\Override]
    public function isVisible(int $characterID): bool
    {
        return true;
    }
}
