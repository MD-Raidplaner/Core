<?php

namespace rp\event\role;

use rp\system\role\RoleItem;
use wcf\event\IPsr14Event;

/**
 * Requests the collection of role items.
 *
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
final class RoleCollecting implements IPsr14Event
{
    /**
     * @var RoleItem[]
     */
    private array $roles = [];

    /**
     * Returns the registered roles.
     *
     * @return RoleItem[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * Registers a role item.
     */
    public function register(RoleItem $role): void
    {
        if (\array_key_exists($role->identifier, $this->roles)) {
            throw new \InvalidArgumentException(\sprintf(
                'Role with identifier %s already exists',
                $role->identifier
            ));
        }

        $this->roles[$role->identifier] = $role;
    }
}
