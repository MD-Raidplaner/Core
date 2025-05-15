<?php

namespace rp\system\option;

use wcf\data\option\Option;
use wcf\system\exception\UserInputException;
use wcf\system\option\AbstractOptionType;
use wcf\system\WCF;

/**
 * Option type implementation for item database selection.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
class ItemDatabasesOptionType extends AbstractOptionType
{
    /**
     * list of available item databases
     */
    protected static ?array $databases = null;

    /**
     * @inheritDoc
     */
    public function getData(Option $option, $newValue): string
    {
        if (!\is_array($newValue)) {
            return '';
        }

        return \implode(',', $newValue);
    }

    /**
     * Returns the list of available item databases.
     */
    protected static function getDatabases(): array
    {
        if (self::$databases === null) {
            self::$databases = [];
            $sql = "SELECT  identifier
                    FROM    rp1_item_database";
            $statement = WCF::getDB()->prepare($sql);
            $statement->execute();
            self::$databases = $statement->fetchAll(\PDO::FETCH_COLUMN);
        }

        return self::$databases;
    }

    /**
     * @inheritDoc
     */
    public function getFormElement(Option $option, $value): string
    {
        $databases = self::getDatabases();
        $selectedDatabases = \explode(',', $value);

        if ($option->issortable) {
            // remove old databases
            $sortedDatabases = \array_intersect($selectedDatabases, $databases);

            // append the non-checked databases after the checked and sorted databases
            $databases = \array_merge($sortedDatabases, \array_diff($databases, $sortedDatabases));
        }

        WCF::getTPL()->assign([
            'option' => $option,
            'value' => $selectedDatabases,
            'availableDatabases' => $databases,
        ]);

        return WCF::getTPL()->fetch('itemDatabaseOptionType', 'rp');
    }

    /**
     * @inheritDoc
     */
    public function validate(Option $option, $newValue): void
    {
        if (!\is_array($newValue)) {
            $newValue = [];
        }

        $availableDatabases = self::getDatabases();

        foreach ($newValue as $identifier) {
            if (!\in_array($identifier, $availableDatabases, true)) {
                throw new UserInputException($option->optionName, 'validationFailed');
            }
        }
    }
}
