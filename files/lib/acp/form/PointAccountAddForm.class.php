<?php

namespace rp\acp\form;

use rp\data\point\account\PointAccount;
use rp\data\point\account\PointAccountAction;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\field\DescriptionFormField;
use wcf\system\form\builder\field\TitleFormField;

/**
 * Shows the point account add form.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @extends AbstractFormBuilderForm<PointAccount>
 */
class PointAccountAddForm extends AbstractFormBuilderForm
{
    public $activeMenuItem = 'rp.acp.menu.link.point.account.add';
    public $neededModules = [
        'RP_POINTS_ENABLED',
        'RP_ITEM_ACCOUNT_EASYMODE_DISABLED'
    ];
    public $neededPermissions = ['admin.rp.canManagePointAccount'];
    public $objectActionClass = PointAccountAction::class;
    public $objectEditLinkApplication = 'rp';
    public $objectEditLinkController = PointAccountEditForm::class;

    #[\Override]
    protected function createForm(): void
    {
        parent::createForm();

        $dataContainer = FormContainer::create('data')
            ->label('wcf.global.form.data')
            ->appendChildren([
                TitleFormField::create()
                    ->autoFocus()
                    ->required()
                    ->maximumLength(255)
                    ->i18n()
                    ->languageItemPattern('rp.point.account.title\d+'),
                DescriptionFormField::create()
                    ->maximumLength(255)
                    ->i18n()
                    ->languageItemPattern('rp.point.account.description\d+'),
            ]);
        $this->form->appendChild($dataContainer);
    }
}
