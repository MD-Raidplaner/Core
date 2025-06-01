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
     */
    public function __construct(
        private readonly int|string $id,
        private readonly string $name,
        private readonly ?string $race = null,
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
     * Return the race of the character.
     */
    public function getRace(): ?string
    {
        return $this->race;
    }

    public function __toString(): string
    {
        return $this->getName();
    }
}
