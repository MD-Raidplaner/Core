<?php

namespace rp\acp\form;

use rp\data\point\account\PointAccountCache;
use rp\data\raid\event\RaidEvent;
use rp\data\raid\event\RaidEventAction;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\data\processor\VoidFormDataProcessor;
use wcf\system\form\builder\field\BooleanFormField;
use wcf\system\form\builder\field\dependency\ValueFormFieldDependency;
use wcf\system\form\builder\field\FloatFormField;
use wcf\system\form\builder\field\RadioButtonFormField;
use wcf\system\form\builder\field\SingleSelectionFormField;
use wcf\system\form\builder\field\TitleFormField;
use wcf\system\form\builder\field\UploadFormField;
use wcf\system\Regex;
use wcf\util\DirectoryUtil;

/**
 * Shows the raid event add form.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @extends AbstractFormBuilderForm<RaidEvent>
 */
class RaidEventAddForm extends AbstractFormBuilderForm
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'rp.acp.menu.link.raid.event.add';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.rp.canManageRaidEvent'];

    /**
     * @inheritDoc
     */
    public $objectActionClass = RaidEventAction::class;

    /**
     * @inheritDoc
     */
    public $objectEditLinkApplication = 'rp';

    /**
     * @inheritDoc
     */
    public $objectEditLinkController = RaidEventEditForm::class;

    /**
     * @inheritDoc
     */
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
                    ->languageItemPattern('rp.raid.event.title\d+'),
                SingleSelectionFormField::create('pointAccountID')
                    ->label('rp.acp.raid.event.point.account')
                    ->options(PointAccountCache::getInstance()->getAccounts()),
                FloatFormField::create('defaultPoints')
                    ->label('rp.acp.raid.event.defaultPoints')
                    ->description('rp.acp.raid.event.defaultPoints.description')
                    ->value(0),
                BooleanFormField::create('showProfile')
                    ->label('rp.acp.raid.event.showProfile')
                    ->value(1)
            ]);

        $iconContainer = FormContainer::create('imageSection')
            ->label('rp.acp.raid.event.image')
            ->appendChildren([
                SingleSelectionFormField::create('imageMode')
                    ->label('rp.acp.raid.event.image.mode')
                    ->options([
                        'select' => 'rp.acp.raid.event.image.mode.select',
                        'upload' => 'rp.acp.raid.event.image.mode.upload'
                    ])
                    ->available($this->formObject === null)
                    ->value('select'),
                RadioButtonFormField::create('icon')
                    ->label('rp.acp.raid.event.image.mode.select')
                    ->required()
                    ->options(function () {
                        $fileNameRegex = new Regex('^[^?]*\.png');
                        $files = DirectoryUtil::getInstance(RP_DIR . 'images/raid/event/')->getFiles(SORT_DESC, $fileNameRegex);

                        $options = [];
                        foreach (\array_flip(
                            \array_map('basename', $files)
                        ) as $filename => $fullName) {
                            $ext = \explode('.', $filename);
                            \array_pop($ext);
                            $options[\implode($ext)] = '<img src="' . RELATIVE_RP_DIR . 'images/raid/event/' . $filename . '" alt="" style="width: 25px; height: 25px"> ' . $filename;
                        }

                        return $options;
                    }, false, false)
                    ->required()
                    ->addClass('floated'),
                UploadFormField::create('iconFile')
                    ->label('rp.acp.raid.event.image.mode.upload')
                    ->required()
                    ->maximum(1)
                    ->imageOnly(true)
                    ->allowSvgImage(true)
                    ->available($this->formObject === null)
            ]);

        $this->form->appendChildren([
            $dataContainer,
            $iconContainer
        ]);

        // `imageMode` is an internal field not meant to be
        // treated as real data
        $this->form->getDataHandler()->addProcessor(new VoidFormDataProcessor('imageMode'));

        /** @var SingleSelectionFormField $modeField */
        $modeField = $iconContainer->getNodeById('imageMode');

        $iconContainer->getNodeById('icon')->addDependency(
            ValueFormFieldDependency::create('modeSelect')
                ->field($modeField)
                ->values(['select'])
        );

        $iconContainer->getNodeById('iconFile')->addDependency(
            ValueFormFieldDependency::create('modeUpload')
                ->field($modeField)
                ->values(['upload'])
        );
    }
}
