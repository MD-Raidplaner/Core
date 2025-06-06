<?php

namespace rp\event\event;

use rp\system\event\IEventController;

/**
 * Requests the collection of event types.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class EventTypeCollecting
{
    /**
     * @var array<string, string>
     */
    private array $types = [];

    /**
     * Returns the event controller for the given type.
     * 
     * @throws \InvalidArgumentException if the type is not registered
     */
    public function getType(string $type): IEventController
    {
        if (!isset($this->types[$type])) {
            throw new \InvalidArgumentException(
                sprintf('Event type "%s" is not registered.', $type)
            );
        }

        return $this->types[$type];
    }

    /**
     * Returns the registered event types.
     * 
     * @return array<string, string> An associative array where keys are event types and values are controller class names.
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * Registers a new event type with its controller class.
     * 
     * @throws \InvalidArgumentException if the type is already registered
     */
    public function register(string $type, string $className): void
    {
        if (in_array($type, $this->types)) {
            throw new \InvalidArgumentException(
                sprintf('Event type "%s" is already registered.', $type)
            );
        }

        $this->types[$type] = $className;
    }
}