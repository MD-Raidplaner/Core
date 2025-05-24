<?php

namespace rp\data\role;

use rp\system\cache\builder\RoleCacheBuilder;
use wcf\system\SingletonFactory;

/**
 * Manages the role cache.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class RoleCache extends SingletonFactory
{
    /**
     * cached role ids with role identifier as key
     * @var int[]
     */
    protected array $cachedIdentifier = [];

    /**
     * cached roles
     * @var Role[]
     */
    protected array $cachedRoles = [];

    /**
     * Returns the role with the given role id or `null` if no such role exists.
     */
    public function getRoleByID(int $roleID): ?Role
    {
        return $this->cachedRoles[$roleID] ?? null;
    }

    /**
     * Returns the role with the given role identifier or `null` if no such role exists.
     */
    public function getRoleByIdentifier(string $identifier): ?Role
    {
        return $this->getRoleByID($this->cachedIdentifier[$identifier] ?? 0);
    }

    /**
     * Returns all roles.
     * 
     * @return	Role[]
     */
    public function getRoles(): array
    {
        return $this->cachedRoles;
    }

    /**
     * Returns the roles with the given role ids.
     * 
     * @return	Role[]
     */
    public function getRolesByIDs(array $roleIDs): array
    {
        return \array_filter(
            \array_map(fn ($roleID) => $this->getEventByID($roleID), $roleIDs),
            fn ($role) => $role !== null
        );
    }

    #[\Override]
    protected function init(): void
    {
        $this->cachedIdentifier = RoleCacheBuilder::getInstance()->getData(['gameID' => RP_CURRENT_GAME_ID], 'identifier');
        $this->cachedRoles = RoleCacheBuilder::getInstance()->getData(['gameID' => RP_CURRENT_GAME_ID], 'role');
    }
}
