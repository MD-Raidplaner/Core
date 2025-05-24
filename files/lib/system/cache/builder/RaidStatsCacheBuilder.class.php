<?php

namespace rp\system\cache\builder;

use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\WCF;

/**
 * Caches raid stats.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class RaidStatsCacheBuilder extends AbstractCacheBuilder
{
    #[\Override]
    protected function rebuild(array $parameters): array
    {
        $stats = [];

        $sql = "SELECT      raid.date, raidEvent.pointAccountID
                FROM        rp1_raid raid
                LEFT JOIN   rp1_raid_event raidEvent
                ON          raid.raidEventID = raidEvent.eventID
                WHERE       raid.gameID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$parameters['gameID']]);

        // Calculate raid statistics for each point account
        while ($row = $statement->fetchArray()) {
            $pointAccountID = $row['pointAccountID'];
            $raidDate = $row['date'];

            // Initialize statistics array if it doesn't exist for this point account
            if (!isset($stats[$pointAccountID])) {
                $stats[$pointAccountID] = [
                    'raid30' => 0,
                    'raid60' => 0,
                    'raid90' => 0,
                    'raidAll' => 0,
                ];
            }

            // Increment raid counters based on the raid date
            $stats[$pointAccountID]['raidAll']++;
            if ($raidDate >= (TIME_NOW - (90 * 86400))) $stats[$pointAccountID]['raid90']++;
            if ($raidDate >= (TIME_NOW - (60 * 86400))) $stats[$pointAccountID]['raid60']++;
            if ($raidDate >= (TIME_NOW - (30 * 86400))) $stats[$pointAccountID]['raid30']++;
        }

        return $stats;
    }
}
