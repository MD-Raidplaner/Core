<?php

namespace rp\system\cache\eager\data;

/**
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class RoleCacheData
{
    public function __construct(
        /** @var array<int, Role> */
        public readonly array $roles,
        /** @var array<string, int> */
        public readonly array $identifiers
    ) {}

    /**
     * Returns the role with the given role id or `null` if no such role exists.
     */
    public function getRole(int $roleID): ?Role
    {
        return $this->roles[$roleID] ?? null;
    }

    /**
     * Returns the role with the given role identifier or `null` if no such role exists.
     */
    public function getRoleByIdentifier(string $identifier): ?Role
    {
        return $this->getRole($this->identifiers[$identifier] ?? 0);
    }

    /**
     * Returns all roles.
     * 
     * @return array<int, Role>
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * Returns the roles with the given role ids.
     * 
     * @param array<int> $roleIDs
     * @return array<int, Role>
     */
    public function getRolesByIDs(array $roleIDs): array
    {
        return \array_filter(
            \array_map(fn($roleID) => $this->getRole($roleID), $roleIDs),
            fn($role) => $role !== null
        );
    }
}
