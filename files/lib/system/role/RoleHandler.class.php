<?php

namespace rp\system\role;

use rp\event\role\RoleCollecting;
use wcf\system\event\EventHandler;
use wcf\system\SingletonFactory;

/**
 * RoleHandler is a singleton factory that manages the roles in the system.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class RoleHandler extends SingletonFactory
{
    /**
     * @var array<string, RoleItem>
     */
    private array $roles = [];

    /**
     * Returns the role with the given role identifier or `null` if no such role exists.
     */
    public function getRoleByIdentifier(string $identifier): ?RoleItem
    {
        return $this->roles[$identifier] ?? null;
    }

    /**
     * Returns all roles that are currently registered.
     * 
     * @return array<string, RoleItem> 
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    #[\Override]
    protected function init(): void
    {
        $event = new RoleCollecting();
        EventHandler::getInstance()->fire($event);
        foreach ($event->getRoles() as $role) {
            if ($role->game !== \RP_CURRENT_GAME) {
                continue; // Only load roles for the current game
            }

            $this->roles[$role->identifier] = $role;
        }
    }
}
