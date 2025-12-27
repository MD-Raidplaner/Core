<?php

namespace rp\form;

use wcf\data\IStorableObject;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Abstract implemation of a form.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 * 
 * @template TIStorableObject of IStorableObject|null
 * @extends AbstractFormBuilderForm<TIStorableObject>
 */
abstract class AbstractForm extends AbstractFormBuilderForm
{
    /**
     * The command action to execute.
     */
    protected string $commandAction;

    #[\Override]
    public function save(): void
    {
        \wcf\form\AbstractForm::save();

        $formData = $this->form->getData();

        $data = $formData['data'] ?? [];
        unset($formData['data']);
        $data = \array_merge($this->additionalFields, $data);

        $object = null;
        if ($this->formAction === 'create') {
            $command = new $this->commandAction($data, $formData);
            $object = $command();
        } else {
            $command = new $this->commandAction($data, $formData, $this->formObject);
            $command();
        }

        $this->saved();

        WCF::getTPL()->assign('success', true);

        if ($this->formAction === 'create' && $this->objectEditLinkController && $object !== null) {
            WCF::getTPL()->assign(
                'objectEditLink',
                LinkHandler::getInstance()->getControllerLink($this->objectEditLinkController, [
                    'id' => $object->getObjectID(),
                ])
            );
        }
    }
}
