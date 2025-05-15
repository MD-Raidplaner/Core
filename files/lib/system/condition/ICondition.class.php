<?php

namespace rp\system\condition;

use wcf\data\DatabaseObjectList;
use wcf\data\IDatabaseObjectProcessor;
use wcf\system\form\builder\field\IFormField;
use wcf\system\form\builder\IFormDocument;

/**
 * Every concrete condition implementation needs to implement this interface.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
interface ICondition extends IDatabaseObjectProcessor
{
    /**
     * Adds a condition to the given object list based on the given condition data.
     * 
     * @throws  \InvalidArgumentException   if the given object list object is no object of the expected database object list class
     */
    public function addObjectListCondition(DatabaseObjectList $objectList, IFormDocument $form, array $conditionData = []): void;

    /**
     * Returns the output form field for setting up the condition.
     */
    public function getFormField(): IFormField;

    /**
     * Returns the id this form field for setting up the condition.
     */
    public function getID(): string;

    /**
     * Returns the data of the form node based on the form node id.
     */
    public function getValue(IFormDocument $form): mixed;

    /**
     * Set the data of the form node based on the form node id.
     */
    public function setValue(mixed $value, IFormDocument $form): void;
}
