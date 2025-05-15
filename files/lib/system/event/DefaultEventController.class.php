<?php

namespace rp\system\event;

use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\field\TitleFormField;
use wcf\system\form\builder\IFormDocument;
use wcf\system\WCF;

/**
 * Default event implementation for event controllers.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
class DefaultEventController extends AbstractEventController
{
    /**
     * @inheritDoc
     */
    protected string $eventController = 'de.md-raidplaner.rp.event.controller.default';

    /**
     * event nodes position
     */
    protected string $eventNodesPosition = 'center';

    /**
     * @inheritDoc
     */
    protected array $savedFields = [
        'enableComments',
        'endTime',
        'isFullDay',
        'notes',
        'startTime',
        'title',
        'userID',
        'username'
    ];

    /**
     * @inheritDoc
     */
    public function createForm(IFormDocument $form): void
    {
        $dataContainer = FormContainer::create('data')
            ->label('wcf.global.form.data')
            ->appendChild(
                TitleFormField::create()
                    ->required()
                    ->maximumLength(191)
            );
        $form->appendChild($dataContainer);

        $this->formEventDate($dataContainer, true);
        $this->formNotes($dataContainer);
        $this->formComment($dataContainer);

        parent::createForm($form);
    }

    /**
     * @inheritDoc
     */
    public function getContent(): string
    {
        return '';
    }
}
