<?php

namespace rp\system\form\builder\field\character;

use wcf\system\form\builder\field\MultipleSelectionFormField;

/**
 * Extended the Implementation of a form field for selecting multiple values for characters.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
class CharacterMultipleSelectionFormField extends MultipleSelectionFormField
{
    protected $templateApplication = 'rp';
    protected $templateName = 'shared_characterMultipleSelectionFormField';

    #[\Override]
    public function options($options, $nestedOptions = false, $labelLanguageItems = true): self
    {
        parent::options($options, $nestedOptions, $labelLanguageItems);

        if ($nestedOptions) {
            foreach ($this->nestedOptions as $key => $option) {
                if (isset($options[$option['value']])) {
                    $this->nestedOptions[$key]['userID'] = $options[$option['value']]['userID'] ?? 0;
                }
            }
        }

        return $this;
    }
}
