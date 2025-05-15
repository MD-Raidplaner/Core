<?php

namespace rp\system\option;

use CuyZ\Valinor\Type\ObjectType;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\option\Option;
use wcf\system\option\SelectOptionType;
use wcf\system\WCF;

/**
 * Option type implementation for event controller select lists.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
class EventControllerSelectOptionType extends SelectOptionType
{
    /**
     * @inheritDoc
     */
    protected function getSelectOptions(Option $option): array
    {
        $availableEventControllers = ObjectTypeCache::getInstance()->getObjectTypes('de.md-raidplaner.rp.event.controller');

        $eventControllers = [];
        /** @var ObjectType $eventController */
        foreach ($availableEventControllers as $eventController) {
            $eventControllers[$eventController->objectType] = WCF::getLanguage()->get('rp.event.controller.' . $eventController->objectType);
        }

        \uasort($eventControllers, function (string $a, string $b) {
            return \strcmp($a, $b);
        });

        return $eventControllers;
    }
}
