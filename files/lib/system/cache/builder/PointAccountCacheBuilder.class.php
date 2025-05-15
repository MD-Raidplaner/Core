<?php

namespace rp\system\cache\builder;

use rp\data\point\account\PointAccount;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\WCF;

/**
 * Caches the point accounts.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class PointAccountCacheBuilder extends AbstractCacheBuilder
{
    /**
     * @inheritDoc
     */
    protected function rebuild(array $parameters): array
    {
        $data = [];

        // get point accounts
        $sql = "SELECT  *
                FROM    rp1_point_account
                WHERE   gameID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$parameters['gameID']]);

        /** @var PointAccount $object */
        while ($object = $statement->fetchObject(PointAccount::class)) {
            $data[$object->accountID] = $object;
        }

        return $data;
    }
}
