<?php

namespace rp\system\form\builder\field;

use wcf\system\form\builder\field\SingleSelectionFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;

/**
 * This class adds dynamic functionalities to the `SingleSelectionFormField`.
 * 
 * It allows the options to be updated automatically based on another selection in the form, 
 * which increases the interactivity and adaptability of the form.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
class DynamicSelectFormField extends SingleSelectionFormField
{
    /**
     * @inheritDoc
     */
    protected $templateApplication = 'rp';

    /**
     * @inheritDoc
     */
    protected $templateName = 'shared_dynamicSelectFormField';

    /**
     * A mapping of options that is used to determine the available options for this form field based on another selection.
     */
    protected ?array $optionsMapping = null;

    /**
     * The name of the selection field that triggers this field and whose selection affects the options in this field.
     */
    protected ?string $triggerSelect = null;

    /**
     * Returns the option mapping that has been configured for this field.
     * 
     * @throws \LogicException If no option mapping has been set.
     */
    public function getOptionsMapping(): array
    {
        if ($this->optionsMapping === null) {
            throw new \LogicException("No options mapping have been set for field '{$this->getId()}'.");
        }

        return $this->optionsMapping;
    }

    /**
     * Returns the associated trigger selection field that affects the options in this field.
     * 
     * @throws \LogicException If the trigger selection field was not set.
     */
    public function getTriggerSelect(): string
    {
        if ($this->triggerSelect === null) {
            throw new \LogicException("\$triggerSelect property has not been set for class '" . static::class . "'.");
        }

        return $this->triggerSelect;
    }

    /**
     * Sets the option mapping for this field. The mapping can either be an array or a callable that returns an array.
     * 
     * @throws \InvalidArgumentException If the passed mapping is neither an array nor a callable.
     * @throws \UnexpectedValueException If the callable does not return an array.
     */
    public function optionsMapping(array|callable $optionsMapping): self
    {
        if (!\is_array($optionsMapping) && !\is_callable($optionsMapping)) {
            throw new \InvalidArgumentException(
                "The given options mapping are neither iterable nor a callable, " . \gettype($optionsMapping) . " given for field '{$this->getId()}'."
            );
        }

        if (\is_callable($optionsMapping)) {
            $optionsMapping = $optionsMapping();

            if (!\is_array($optionsMapping)) {
                throw new \UnexpectedValueException(
                    "The options mapping callable is expected to return an iterable value, " . \gettype($options) . " returned for field '{$this->getId()}'."
                );
            }

            return $this->optionsMapping($optionsMapping);
        }

        $this->optionsMapping = [];
        foreach ($optionsMapping as $key => $values) {
            if (!\is_array($values)) {
                throw new \InvalidArgumentException(
                    "Options mapping must not contain any array. Array given for key '{$key}' for field '{$this->getId()}'."
                );
            }

            foreach ($values as $value) {
                if (!\is_numeric($value)) {
                    throw new \InvalidArgumentException(
                        "Options mapping values contain invalid values of type " . \gettype($value) . " for field '{$this->getId()}'."
                    );
                }
            }

            if (isset($this->optionsMapping[$key])) {
                throw new \InvalidArgumentException(
                    "Options mapping values must be unique, but '{$key}' appears at least twice as value for field '{$this->getId()}'."
                );
            }

            $this->optionsMapping[$key] = $values;
        }

        return $this;
    }

    /**
     * Sets the trigger selection field that affects the options in this field.
     */
    public function triggerSelect(string $select): self
    {
        $this->triggerSelect = $select;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function validate(): void
    {
        parent::validate();

        $formField = $this->getDocument()->getNodeById($this->triggerSelect);
        $triggerValue = $formField->getValue();

        if (
            !isset($this->getOptionsMapping()[$triggerValue]) ||
            !\in_array($this->getValue(), $this->getOptionsMapping()[$triggerValue])
        ) {
            if (!\count($this->getValidationErrors())) {
                $this->addValidationError(new FormFieldValidationError(
                    'invalidValue',
                    'wcf.global.form.error.noValidSelection'
                ));
            }
        }
    }
}
