<?php

namespace rp\system\character;

/**
 * Represents an available character with an ID, name, classification ID, and role ID.
 *
 * The AvailableCharacter class is immutable and provides methods to access its properties.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class AvailableCharacter
{
    /**
     * Constructor for the AvailableCharacter class.
     *
     * @param int|string $id        The identifier of the character. It can be an integer or a string.
     * @param string $name      The name of the character.
     * @param int|null $raceID      The race id of the character. It is optional and can be null.
     * @param int|null $classificationID        The classification id of the character. It is optional and can be null.
     * @param int|null $roleID      The role id of the character. It is optional and can be null.
     */
    public function __construct(
        private readonly int|string $id,
        private readonly string $name,
        private readonly ?int $raceID = null,
        private readonly ?int $classificationID = null
    ) {
    }

    /**
     * Return the classification id of the character.
     */
    public function getClassificationID(): ?int
    {
        return $this->classificationID;
    }

    /**
     * Return the unique identifier of the character.
     */
    public function getID(): int|string
    {
        return $this->id;
    }

    /**
     * Return the name of the character.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Return the race id of the character.
     */
    public function getRaceID(): ?int
    {
        return $this->raceID;
    }

    public function __toString(): string
    {
        return $this->getName();
    }
}
