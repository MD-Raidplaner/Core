<?php

namespace rp\acp\form;

use rp\command\character\CreateCharacter;
use rp\data\character\Character;
use rp\event\character\CharacterAddAttribute;
use rp\form\AbstractForm;
use wcf\system\event\EventHandler;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\container\TabFormContainer;
use wcf\system\form\builder\container\TabMenuFormContainer;
use wcf\system\form\builder\container\TabTabMenuFormContainer;
use wcf\system\form\builder\data\processor\CustomFormDataProcessor;
use wcf\system\form\builder\field\MultilineTextFormField;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\user\UserFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\form\builder\IFormDocument;
use wcf\system\request\LinkHandler;
use wcf\system\request\RequestHandler;
use wcf\system\WCF;

/**
 * Shows the character add form.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 * 
 * @extends AbstractForm<Character>
 */
class CharacterAddForm extends AbstractForm
{
    public $activeMenuItem = 'rp.acp.menu.link.character.add';

    /**
     * ids of the fields containing object data
     * @var list<string>
     */
    public array $characterFields = [
        'characterName',
        'guildName',
        'notes',
        'userID',
        'username',
    ];

    protected string $commandAction = CreateCharacter::class;

    public $neededPermissions = ['admin.rp.canAddCharacter'];

    #[\Override]
    protected function createForm(): void
    {
        parent::createForm();

        $tabMenu = TabMenuFormContainer::create('characterTabMenu');
        $this->form->appendChild($tabMenu);

        // data tab
        $dataTab = TabFormContainer::create('dataTab');
        $dataTab->label('wcf.global.form.data');
        $tabMenu->appendChild($dataTab);

        $dataContainer = FormContainer::create('data')
            ->appendChildren([
                TextFormField::create('characterName')
                    ->label('rp.character.characterName')
                    ->required()
                    ->autoFocus()
                    ->maximumLength(100)
                    ->addValidator(new FormFieldValidator('uniqueness', function (TextFormField $formField) {
                        $value = $formField->getSaveValue();

                        if (
                            $this->formAction === IFormDocument::FORM_MODE_CREATE
                            || ($value !== $this->formObject->characterName)
                        ) {

                            $character = Character::getCharacterByName($value);
                            if ($character->getObjectID()) {
                                $formField->addValidationError(
                                    new FormFieldValidationError(
                                        'uniqueness',
                                        'rp.character.characterName.error.uniqueness'
                                    )
                                );
                            }
                        }
                    })),
                UserFormField::create('userID')
                    ->label('wcf.user.username')
                    ->available(RequestHandler::getInstance()->isACPRequest())
                    ->required(),
                TextFormField::create('guildName')
                    ->label('rp.character.guildName')
                    ->maximumLength(100),
                MultilineTextFormField::create('notes')
                    ->label('rp.character.notes'),
            ]);
        $dataTab->appendChild($dataContainer);

        // character tab
        $attributeTab = TabTabMenuFormContainer::create('attributeTab');
        $attributeTab->label('rp.character.category.attributes');
        $tabMenu->appendChild($attributeTab);

        $attributeGeneralTab = TabFormContainer::create('attributeGeneralTab')
            ->label('rp.character.category.attributes')
            ->appendChild(FormContainer::create('attributeGeneralSection'));
        $attributeTab->appendChild($attributeGeneralTab);

        EventHandler::getInstance()->fire(
            new CharacterAddAttribute($attributeTab)
        );

        $this->form->getDataHandler()->addProcessor(
            new CustomFormDataProcessor(
                'customs',
                static function (IFormDocument $document, array $parameters) {
                    if (!RequestHandler::getInstance()->isACPRequest()) {
                        $parameters['data']['userID'] = WCF::getUser()->getObjectID();
                    }

                    $parameters['data']['userID'] ??= null;

                    return $parameters;
                }
            )
        );
    }

    #[\Override]
    public function save(): void
    {
        \wcf\form\AbstractForm::save();

        $formData = $this->form->getData();

        $data = $formData['data'] ?? [];

        $formData['data'] = [
            ...$this->additionalFields,
            ...$formData['data'],
        ];

        $characterData = [
            'game' => \RP_CURRENT_GAME,
        ];

        foreach ($this->characterFields as $field) {
            if (isset($formData['data'][$field])) {
                $characterData[$field] = $formData['data'][$field];
                unset($formData['data'][$field]);
            }
        }

        if (!isset($characterData['userID']) || $characterData['userID'] === 0) {
            $characterData['userID'] = null;
        }

        $characterData['additionalData'] = \serialize($formData['data']);
        unset($formData['data']);

        $object = null;
        if ($this->formAction === 'create') {
            $command = new $this->commandAction($characterData, $formData);
            $object = $command();
            \assert($object instanceof Character);
        } else {
            $command = new $this->commandAction($characterData, $formData, $this->formObject);
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
