<?php

namespace rp\system\event;

use rp\data\event\Event;
use rp\event\event\EventCreateForm;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\event\EventHandler;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\form\builder\container\IFormContainer;
use wcf\system\form\builder\data\processor\CustomFormDataProcessor;
use wcf\system\form\builder\data\processor\VoidFormDataProcessor;
use wcf\system\form\builder\field\BooleanFormField;
use wcf\system\form\builder\field\DateFormField;
use wcf\system\form\builder\field\dependency\EmptyFormFieldDependency;
use wcf\system\form\builder\field\dependency\NonEmptyFormFieldDependency;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\form\builder\field\wysiwyg\WysiwygFormField;
use wcf\system\form\builder\IFormDocument;
use wcf\system\style\FontAwesomeIcon;
use wcf\system\WCF;

/**
 * Default implementation for event controllers.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
abstract class AbstractEventController implements IEventController
{
    /**
     * database object of this event
     */
    protected ?Event $event = null;

    /**
     * type name of this event controller
     */
    protected string $eventController = '';

    /**
     * Position where Notes should be displayed in this event.
     */
    protected string $eventNodesPosition = '';

    /**
     * ids of the fields containing object data
     */
    protected array $savedFields = [];

    #[\Override]
    public function checkPermissions(): void
    {
        if (!$this->event->isVisible()) {
            throw new PermissionDeniedException();
        }
    }

    #[\Override]
    public function createForm(IFormDocument $form): void
    {
        $event = new EventCreateForm($form, $this->eventController);
        EventHandler::getInstance()->fire($event);
    }

    /**
     * Adds a Boolean form field for enabling comments.
     */
    protected function formComment(IFormContainer $container): void
    {
        $container->appendChild(
            BooleanFormField::create('enableComments')
                ->label('rp.event.enableComments')
                ->value(1)
        );
    }

    /**
     * Adds an event date to the container.
     */
    protected function formEventDate(IFormContainer $container, bool $supportFullDay = false): void
    {
        $isFullDay = BooleanFormField::create('isFullDay')
            ->label('rp.event.isFullDay')
            ->value(0)
            ->available($supportFullDay);

        $container->appendChildren([
            $isFullDay,
            DateFormField::create('startTime')
                ->label('rp.event.startTime')
                ->required()
                ->supportTime()
                ->value(TIME_NOW)
                ->addValidator(new FormFieldValidator('uniqueness', function (DateFormField $formField) {
                    $value = $formField->getSaveValue();
                    if ($value === null || $value < -2147483647 || $value > 2147483647) {
                        $formField->addValidationError(
                            new FormFieldValidationError(
                                'invalid',
                                'rp.event.startTime.error.invalid'
                            )
                        );
                    }
                })),
            DateFormField::create('endTime')
                ->label('rp.event.endTime')
                ->required()
                ->supportTime()
                ->value(TIME_NOW + 7200) // +2h
                ->addValidator(new FormFieldValidator('uniqueness', function (DateFormField $formField) {
                    /** @var DateFormField $startFormField */
                    $startFormField = $formField->getDocument()->getNodeById('startTime');
                    $startValue = $startFormField->getSaveValue();

                    $value = $formField->getSaveValue();

                    if ($value === null || $value < $startValue || $value > 2147483647) {
                        $formField->addValidationError(
                            new FormFieldValidationError(
                                'invalid',
                                'rp.event.endTime.error.invalid'
                            )
                        );
                    }
                }))
                ->addValidator(new FormFieldValidator('long', function (DateFormField $formField) {
                    /** @var DateFormField $startFormField */
                    $startFormField = $formField->getDocument()->getNodeById('startTime');
                    $startValue = $startFormField->getSaveValue();

                    $value = $formField->getSaveValue();

                    if ($value - $startValue > RP_CALENDAR_MAX_EVENT_LENGTH * 86400) {
                        $formField->addValidationError(
                            new FormFieldValidationError(
                                'tooLong',
                                'rp.event.endTime.error.tooLong'
                            )
                        );
                    }
                })),
            DateFormField::create('fStartTime')
                ->label('rp.event.startTime')
                ->required()
                ->value(TIME_NOW)
                ->addValidator(new FormFieldValidator('uniqueness', function (DateFormField $formField) {
                    $value = $formField->getSaveValue();
                    if ($value === null || $value < -2147483647 || $value > 2147483647) {
                        $formField->addValidationError(
                            new FormFieldValidationError(
                                'invalid',
                                'rp.event.startTime.error.invalid'
                            )
                        );
                    }
                }))
                ->addDependency(
                    NonEmptyFormFieldDependency::create('isFullDay')
                        ->field($isFullDay)
                ),
            DateFormField::create('fEndTime')
                ->label('rp.event.endTime')
                ->required()
                ->value(TIME_NOW + 7200) // +2h
                ->addValidator(new FormFieldValidator('uniqueness', function (DateFormField $formField) {
                    /** @var DateFormField $startFormField */
                    $startFormField = $formField->getDocument()->getNodeById('fStartTime');
                    $startValue = $startFormField->getSaveValue();

                    $value = $formField->getSaveValue();

                    if ($value === null || $value < $startValue || $value > 2147483647) {
                        $formField->addValidationError(
                            new FormFieldValidationError(
                                'invalid',
                                'rp.event.endTime.error.invalid'
                            )
                        );
                    }
                }))
                ->addValidator(new FormFieldValidator('long', function (DateFormField $formField) {
                    /** @var DateFormField $startFormField */
                    $startFormField = $formField->getDocument()->getNodeById('fStartTime');
                    $startValue = $startFormField->getSaveValue();

                    $value = $formField->getSaveValue();

                    if ($value - $startValue > RP_CALENDAR_MAX_EVENT_LENGTH * 86400) {
                        $formField->addValidationError(
                            new FormFieldValidationError(
                                'tooLong',
                                'rp.event.endTime.error.tooLong'
                            )
                        );
                    }
                }))
                ->addDependency(
                    NonEmptyFormFieldDependency::create('isFullDay')
                        ->field($isFullDay)
                ),
        ]);

        $form = $container->getDocument();

        if ($supportFullDay) {
            foreach (['startTime', 'endTime'] as $id) {
                $formField = $form->getNodeById($id);
                $formField?->addDependency(
                    EmptyFormFieldDependency::create('isFullDay')
                        ->field($isFullDay)
                );
            }
        }

        $form->getDataHandler()->addProcessor(new VoidFormDataProcessor('startTime'));
        $form->getDataHandler()->addProcessor(new VoidFormDataProcessor('endTime'));
        $form->getDataHandler()->addProcessor(new VoidFormDataProcessor('fStartTime'));
        $form->getDataHandler()->addProcessor(new VoidFormDataProcessor('fEndTime'));

        $form->getDataHandler()->addProcessor(
            new CustomFormDataProcessor(
                'eventDate',
                static function (IFormDocument $document, array $parameters) {
                    /** @var BooleanFormField $fullDay */
                    $fullDay = $document->getNodeById('isFullDay');
                    /** @var DateFormField $startTime */
                    $startTime = $document->getNodeById($fullDay->getSaveValue() ? 'fStartTime' : 'startTime');
                    /** @var DateFormField $endTime */
                    $endTime = $document->getNodeById($fullDay->getSaveValue() ? 'fEndTime' : 'endTime');

                    $parameters['data']['startTime'] = $startTime->getSaveValue();
                    $parameters['data']['endTime'] = $endTime->getSaveValue();
                    $parameters['data']['timezone'] =  'UTC';

                    return $parameters;
                }
            )
        );
    }

    /**
     * Adds a wysiwyg form field for notes.
     */
    protected function formNotes(IFormContainer $container): void
    {
        $container->appendChild(
            WysiwygFormField::create('notes')
                ->label('rp.event.notes')
                ->objectType('de.md-raidplaner.rp.event.notes')
        );
    }

    #[\Override]
    public function getContentHeaderNavigation(): string
    {
        return '';
    }

    #[\Override]
    public function getEvent(): ?Event
    {
        return $this->event;
    }

    #[\Override]
    public function getIcon(int $size = 16): string
    {
        $fa = FontAwesomeIcon::fromValues('calendar-days', true);
        return $fa->toHtml($size);
    }

    #[\Override]
    public function getTitle(): string
    {
        return $this->getEvent()->title;
    }

    #[\Override]
    public function isAccessible(): bool
    {
        return WCF::getSession()->getPermission('user.rp.canCreateEvent');
    }

    #[\Override]
    public function isExpired(): bool
    {
        return false;
    }

    #[\Override]
    public function saveForm(array $formData): array
    {
        if (empty($this->savedFields)) return $formData;

        $data = [];
        $data = \array_intersect_key($formData['data'], \array_flip($this->savedFields)) + $data;
        $formData['data'] = \array_diff_key($formData['data'], \array_flip($this->savedFields));

        $data['objectTypeID'] = (ObjectTypeCache::getInstance()->getObjectTypeByName('de.md-raidplaner.rp.event.controller', $this->eventController))->objectTypeID;

        $data['additionalData'] = \serialize($formData['data']);
        unset($formData['data']);

        return \array_merge(
            $formData,
            ['data' => $data]
        );
    }

    #[\Override]
    public function setEvent(Event $event): void
    {
        $this->event = $event;
    }

    #[\Override]
    public function showEventNodes(string $position): bool
    {
        return ($this->eventNodesPosition === $position);
    }
}
