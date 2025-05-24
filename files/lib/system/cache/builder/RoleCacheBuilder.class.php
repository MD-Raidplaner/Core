<?php

namespace rp\system\cache\builder;

use rp\data\role\Role;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\WCF;

/**
 * Caches the role.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class RoleCacheBuilder extends AbstractCacheBuilder
{
    #[\Override]
    protected function rebuild(array $parameters): array
    {
        $data = [
            'role' => [],
            'identifier' => [],
        ];

        // get game role
        $sql = "SELECT  *
                FROM    rp1_role
                WHERE   isDisabled = ?
                    AND gameID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([
            0,
            $parameters['gameID'],
        ]);

        /** @var Role $object */
        while ($object = $statement->fetchObject(Role::class)) {
            $data['role'][$object->roleID] = $object;
            $data['identifier'][$object->identifier] = $object->roleID;
        }

        return $data;
    }
}
