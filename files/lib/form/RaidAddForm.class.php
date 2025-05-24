<?php

namespace rp\form;

use CuyZ\Valinor\Mapper\MappingError;
use rp\data\character\CharacterList;
use rp\data\event\Event;
use rp\data\event\EventEditor;
use rp\data\point\account\PointAccountCache;
use rp\data\raid\event\RaidEventCache;
use rp\data\raid\RaidAction;
use rp\event\raid\character\CharacterCollecting;
use rp\system\cache\runtime\EventRuntimeCache;
use rp\system\form\builder\field\character\CharacterMultipleSelectionFormField;
use rp\system\form\builder\field\item\ItemFormField;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\form\AbstractForm;
use wcf\form\AbstractFormBuilderForm;
use wcf\http\Helper;
use wcf\system\event\EventHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\container\TabFormContainer;
use wcf\system\form\builder\container\TabMenuFormContainer;
use wcf\system\form\builder\field\DateFormField;
use wcf\system\form\builder\field\FloatFormField;
use wcf\system\form\builder\field\MultipleSelectionFormField;
use wcf\system\form\builder\field\SingleSelectionFormField;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\request\IRouteController;
use wcf\system\request\LinkHandler;
use wcf\util\HeaderUtil;

/**
 * Shows the raid add form.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
class RaidAddForm extends AbstractFormBuilderForm
{
    public ?Event $event = null;
    public $neededPermissions = ['mod.rp.canAddRaid'];
    public $objectActionClass = RaidAction::class;

    #[\Override]
    public function checkPermissions(): void
    {
        if ($this->formAction == 'create' && $this->event !== null) {
            if ($this->event->getController()->isLeader()) {
                $this->neededPermissions = [];
            } else {
                throw new PermissionDeniedException();
            }
        }

        parent::checkPermissions();
    }

    #[\Override]
    protected function createForm(): void
    {
        parent::createForm();

        $tabMenu = TabMenuFormContainer::create('raidTabMenu');
        $this->form->appendChild($tabMenu);

        // raid tab
        $raidTab = TabFormContainer::create('raidTab')
            ->label('rp.raid.title');
        $tabMenu->appendChild($raidTab);

        $dataContainer = FormContainer::create('data')
            ->label('wcf.global.form.data')
            ->appendChildren([
                DateFormField::create('time')
                    ->label('rp.raid.time')
                    ->required()
                    ->supportTime()
                    ->latestDate(TIME_NOW),
                SingleSelectionFormField::create('raidEventID')
                    ->label('rp.raid.event.title')
                    ->required()
                    ->options(function () {
                        $options = [];
                        $pointAccounts = PointAccountCache::getInstance()->getAccounts();
                        $raidEvents = RaidEventCache::getInstance()->getEvents();

                        // Map raid events by pointAccountID for quick lookup
                        $raidEventsByPointAccount = [];
                        foreach ($raidEvents as $raidEvent) {
                            if ($raidEvent->pointAccountID !== null) {
                                $raidEventsByPointAccount[$raidEvent->pointAccountID][] = $raidEvent;
                            }
                        }

                        foreach ($pointAccounts as $pointAccount) {
                            // Add point account option
                            $options[] = [
                                'depth' => 0,
                                'isSelectable' => false,
                                'label' => $pointAccount->getTitle(),
                                'value' => '',
                            ];

                            // Add related raid events if they exist
                            if (isset($raidEventsByPointAccount[$pointAccount->getObjectID()])) {
                                foreach ($raidEventsByPointAccount[$pointAccount->getObjectID()] as $raidEvent) {
                                    $options[] = [
                                        'depth' => 1,
                                        'label' => $raidEvent->getTitle(),
                                        'value' => $raidEvent->getObjectID(),
                                    ];
                                }
                            }

                            // Remove processed raid events from the main array
                            unset($raidEventsByPointAccount[$pointAccount->getObjectID()]);
                        }

                        // Add remaining raid events
                        foreach ($raidEventsByPointAccount as $events) {
                            foreach ($events as $raidEvent) {
                                $options[] = [
                                    'depth' => 0,
                                    'label' => $raidEvent->getTitle(),
                                    'value' => $raidEvent->getObjectID(),
                                ];
                            }
                        }

                        return $options;
                    }, true, false),
                FloatFormField::create('points')
                    ->label('rp.raid.points')
                    ->available(RP_POINTS_ENABLED)
                    ->minimum(0)
                    ->value(0),
                TextFormField::create('notes')
                    ->label('rp.raid.notes')
                    ->maximumLength(255),
            ]);
        $raidTab->appendChild($dataContainer);

        // item tab
        $itemsTab = TabFormContainer::create('itemsTab');
        $itemsTab->label('rp.raid.items');
        $tabMenu->appendChild($itemsTab);

        $itemsContainer = FormContainer::create('itemsContainer')
            ->appendChild(
                ItemFormField::create()
                    ->available(RP_ENABLE_ITEM)
            );
        $itemsTab->appendChild($itemsContainer);

        if ($this->formAction === 'create' && $this->event === null) {
            $charactersFormField = CharacterMultipleSelectionFormField::create('attendeeIDs')
                ->label('rp.raid.attendees')
                ->filterable()
                ->required()
                ->addValidator(new FormFieldValidator('empty', static function (MultipleSelectionFormField $formField) {
                    if (empty($formField->getSaveValue())) {
                        $formField->addValidationError(new FormFieldValidationError('empty'));
                    }
                }));
            $dataContainer->appendChild($charactersFormField);

            $event = new CharacterCollecting();
            EventHandler::getInstance()->fire($event);

            $characters = $event->getCharacters();
            if (empty($characters)) {
                $characterList = new CharacterList();
                $characterList->getConditionBuilder()->add('member.gameID = ?', [RP_CURRENT_GAME_ID]);
                $characterList->getConditionBuilder()->add('member.isDisabled = ?', [0]);
                $characterList->readObjects();

                foreach ($characterList as $character) {
                    $characters[] = [
                        'id' => $character->getObjectID(),
                        'label' => $character->getTitle(),
                        'userID' => $character->userID,
                    ];
                }
            }

            if (!empty($characters)) {
                $options = [];
                foreach ($characters as $character) {
                    $options[] = [
                        'depth' => 0,
                        'label' => $character['label'],
                        'userID' => $character['userID'],
                        'value' => $character['id'],
                    ];
                }

                $charactersFormField->options($options, true);
            }
        }
    }

    #[\Override]
    public function readData(): void
    {
        parent::readData();

        if (empty($_POST) && $this->formAction === 'create' && $this->event !== null) {
            /** @var DateFormField $dateFormField */
            $dateFormField = $this->form->getNodeById('time');
            $dateFormField->value($this->event->startTime);

            /** @var SingleSelectionFormField $raidEventFormField */
            $raidEventFormField = $this->form->getNodeById('raidEventID');
            $raidEventFormField->value($this->event->raidEventID);

            /** @var FloatFormField $pointsFormField */
            $pointsFormField = $this->form->getNodeById('points');
            $pointsFormField->value($this->event->points);

            /** @var TextFormField $notesFormField */
            $notesFormField = $this->form->getNodeById('notes');
            $notesFormField->value($this->event->getPlainFormattedNotes());
        }
    }

    #[\Override]
    public function readParameters(): void
    {
        parent::readParameters();

        try {
            $parameters = Helper::mapQueryParameters(
                $_GET,
                <<<'EOT'
                array {
                    eventID?: int
                }
                EOT
            );

            if (isset($parameters['eventID'])) {
                $this->event = EventRuntimeCache::getInstance()->getObject($parameters['eventID']);

                if (!$this->event->isRaidEvent() || $this->event->raidID) {
                    throw new IllegalLinkException();
                }
            }
        } catch (MappingError) {
            throw new IllegalLinkException();
        }
    }

    #[\Override]
    public function save(): void
    {
        AbstractForm::save();

        $action = $this->formAction;
        if ($this->objectActionName) {
            $action = $this->objectActionName;
        } elseif ($this->formAction === 'edit') {
            $action = 'update';
        }

        $formData = $this->form->getData();

        $formData['data'] ??= [];
        $formData['data'] = \array_merge($this->additionalFields, $formData['data']);
        $formData['event'] = $this->event;

        wcfDebug($formData);

        $this->objectAction = new $this->objectActionClass(
            \array_filter([$this->formObject]),
            $action,
            $formData
        );
        $this->objectAction->executeAction();

        $this->saved();
    }

    #[\Override]
    public function saved(): void
    {
        if ($this->event !== null) {
            $eventEditor = new EventEditor($this->event);
            $eventEditor->update([
                'raidID' => ($this->objectAction->getReturnValues()['returnValues'])->getObjectID(),
            ]);
        }

        AbstractForm::saved();

        HeaderUtil::redirect(LinkHandler::getInstance()->getLink('RaidList', ['application' => 'rp']));
        exit;
    }

    #[\Override]
    protected function setFormAction(): void
    {
        $parameters = [];

        if ($this->formAction == 'create' && $this->event !== null) {
            $parameters['eventID'] = $this->event->eventID;
        }

        if ($this->formObject !== null) {
            if ($this->formObject instanceof IRouteController) {
                $parameters['object'] = $this->formObject;
            } else {
                $object = $this->formObject;

                $parameters['id'] = $object->{$object::getDatabaseTableIndexName()};
            }
        }

        $this->form->action(LinkHandler::getInstance()->getControllerLink(static::class, $parameters));
    }
}
