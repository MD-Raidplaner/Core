<?php

namespace rp\acp\form;

use CuyZ\Valinor\Mapper\MappingError;
use rp\data\character\Character;
use rp\event\character\CharacterEditData;
use wcf\http\Helper;
use wcf\system\event\EventHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\form\builder\field\IFormField;

/**
 * Shows the character edit form.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
class CharacterEditForm extends CharacterAddForm
{
    public $activeMenuItem = 'rp.acp.menu.link.character.list';
    public $formAction = 'edit';
    public $neededPermissions = ['admin.rp.canEditCharacter'];

    #[\Override]
    public function readParameters(): void
    {
        parent::readParameters();

        try {
            $parameters = Helper::mapQueryParameters(
                $_GET,
                <<<'EOT'
                    array {
                        id: positive-int
                    }
                    EOT
            );
            $this->formObject = new Character($parameters['id']);

            if (!$this->formObject->getObjectID()) {
                throw new IllegalLinkException();
            }
        } catch (MappingError) {
            throw new IllegalLinkException();
        }
    }

    #[\Override]
    protected function setFormObjectData(): void
    {
        parent::setFormObjectData();

        if (empty($_POST)) {
            foreach ($this->formObject->additionalData as $key => $value) {
                $field = $this->form->getNodeById($key);
                if ($field === null) {
                    continue;
                }

                \assert($field instanceof IFormField);
                $field->value($value);
            }

            EventHandler::getInstance()->fire(
                new CharacterEditData($this->form, $this->formObject)
            );
        }
    }
}
