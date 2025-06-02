<?php

namespace rp\event\classification;

use rp\system\classification\ClassificationItem;
use wcf\event\IPsr14Event;

/**
 * Requests the collection of classification items.
 *
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
final class ClassificationCollecting implements IPsr14Event
{
    /**
     * @var ClassificationItem[]
     */
    private array $classifications = [];

    /**
     * Returns the registered classifications.
     *
     * @return ClassificationItem[]
     */
    public function getClassifications(): array
    {
        return $this->classifications;
    }

    /**
     * Registers a classification item.
     */
    public function register(ClassificationItem $classification): void
    {
        if (\array_key_exists($classification->identifier, $this->classifications)) {
            throw new \InvalidArgumentException(\sprintf(
                'Classification with identifier %s already exists',
                $classification->identifier
            ));
        }

        $this->classifications[$classification->identifier] = $classification;
    }
}
