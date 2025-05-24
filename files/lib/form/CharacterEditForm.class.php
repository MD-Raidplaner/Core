<?php

namespace rp\form;

use wcf\system\exception\PermissionDeniedException;

/**
 * Shows the character add form.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
class CharacterEditForm extends \rp\acp\form\CharacterEditForm
{
    public $neededPermissions = [];

    #[\Override]
    public function readParameters(): void
    {
        parent::readParameters();

        if (!$this->formObject->canEdit()) {
            throw new PermissionDeniedException();
        }
    }
}
