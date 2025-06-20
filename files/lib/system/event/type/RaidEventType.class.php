<?php

namespace rp\system\event\type;

use rp\data\character\CharacterList;
use rp\data\event\raid\attendee\EventRaidAttendee;
use rp\data\event\raid\attendee\EventRaidAttendeeList;
use rp\event\character\AvailableCharactersChecking;
use rp\system\cache\eager\PointAccountCache;
use rp\system\cache\eager\RaidEventCache;
use rp\system\cache\runtime\CharacterProfileRuntimeCache;
use rp\system\character\CharacterHandler;
use rp\system\classification\ClassificationHandler;
use rp\system\form\builder\field\character\CharacterMultipleSelectionFormField;
use rp\system\game\GameHandler;
use rp\system\role\RoleHandler;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\event\EventHandler;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\container\TabFormContainer;
use wcf\system\form\builder\container\TabMenuFormContainer;
use wcf\system\form\builder\field\dependency\ValueFormFieldDependency;
use wcf\system\form\builder\field\FloatFormField;
use wcf\system\form\builder\field\IntegerFormField;
use wcf\system\form\builder\field\SingleSelectionFormField;
use wcf\system\form\builder\field\wysiwyg\WysiwygFormField;
use wcf\system\form\builder\IFormDocument;
use wcf\system\WCF;

/**
 * Raid event implementation for event types.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class RaidEventType extends AbstractEventType
{
    /**
     * content data
     */
    protected ?array $contentData = null;
    protected string $eventType = 'raid';
    protected string $eventNodesPosition = 'right';
    protected array $savedFields = [
        'enableComments',
        'endTime',
        'notes',
        'startTime',
        'title',
        'userID',
        'username'
    ];

    /**
     * Creates a class distribution form container for the tab participant
     */
    protected function createClassDistribution(TabFormContainer $tab, SingleSelectionFormField $mode): void
    {
        $classDistributionContainer = FormContainer::create('classDistribution')
            ->label('rp.event.raid.distribution.class');

        foreach (ClassificationHandler::getInstance()->getClassifications() as $classification) {
            $classDistributionContainer->appendChild(
                IntegerFormField::create($classification->identifier)
                    ->label($classification->getTitle())
                    ->minimum(0)
                    ->value(0)
            );
        }

        $classDistributionContainer->addDependency(
            ValueFormFieldDependency::create('classSelect')
                ->field($mode)
                ->values(['class'])
        );

        $tab->appendChild($classDistributionContainer);
    }

    #[\Override]
    public function createForm(IFormDocument $form): void
    {
        $tabMenu = TabMenuFormContainer::create('raidEventTab');
        $form->appendChild($tabMenu);

        // data tab
        $dataTab = TabFormContainer::create('dataTab')
            ->label('wcf.global.form.data');
        $tabMenu->appendChild($dataTab);

        $characters = [];
        $characterList = new CharacterList();
        $characterList->getConditionBuilder()->add('game = ?', [\RP_CURRENT_GAME]);
        $characterList->getConditionBuilder()->add('isDisabled = ?', [0]);
        $characterList->sqlOrderBy = 'characterName ASC';
        $characterList->readObjects();

        foreach ($characterList as $character) {
            $characters[] = [
                'depth' => 0,
                'label' => $character->getTitle(),
                'userID' => $character->userID,
                'value' => $character->getObjectID(),
            ];
        }

        $dataContainer = FormContainer::create('data')
            ->label('wcf.global.form.data')
            ->appendChildren([
                SingleSelectionFormField::create('raidEventID')
                    ->label('rp.raid.event.title')
                    ->required()
                    ->options(function () {
                        $options = [];
                        $pointAccounts = (new PointAccountCache())->getCache()->getAccounts();
                        $raidEvents = (new RaidEventCache())->getCache()->getEvents();

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
                    ->label('rp.event.raid.points')
                    ->description('rp.event.raid.points.description')
                    ->available(RP_POINTS_ENABLED)
                    ->minimum(0)
                    ->value(0),
                CharacterMultipleSelectionFormField::create('leaders')
                    ->label('rp.event.raid.leaders')
                    ->filterable()
                    ->options($characters, true)
                    ->addClass('eventAddLeader'),
            ]);
        $dataTab->appendChild($dataContainer);

        $this->formEventDate($dataContainer);

        $dataContainer->appendChild(
            IntegerFormField::create('deadline')
                ->label('rp.event.raid.deadline')
                ->description('rp.event.raid.deadline.description')
                ->minimum(0)
                ->maximum(24)
                ->value(1)
        );

        $this->formNotes($dataContainer);
        $this->formComment($dataContainer);

        // condition tab
        $conditionTab = TabFormContainer::create('conditionTab')
            ->label('rp.event.raid.condition');
        $tabMenu->appendChild($conditionTab);

        $conditionContainer = FormContainer::create('condition')
            ->label('rp.event.raid.condition')
            ->description('rp.event.raid.condition.description');
        $conditionTab->appendChild($conditionContainer);

        // participant tab
        $participantTab = TabFormContainer::create('participantTab')
            ->label('rp.event.raid.participants');
        $tabMenu->appendChild($participantTab);

        $distributionMode = SingleSelectionFormField::create('distributionMode')
            ->label('rp.event.raid.distribution')
            ->options(static function () {
                return [
                    'class' => 'rp.event.raid.distribution.class',
                    'role' => 'rp.event.raid.distribution.role',
                    'none' => 'rp.event.raid.distribution.none',
                ];
            })
            ->value('role');

        $participantContainer = FormContainer::create('participant')
            ->appendChildren([
                $distributionMode,
                IntegerFormField::create('participants')
                    ->label('rp.event.raid.participants')
                    ->minimum(0)
                    ->value(0)
                    ->addDependency(
                        ValueFormFieldDependency::create('noneSelect')
                            ->field($distributionMode)
                            ->values(['none'])
                    )
            ]);
        $participantTab->appendChild($participantContainer);

        $this->createClassDistribution($participantTab, $distributionMode);
        $this->createRoleDistribution($participantTab, $distributionMode);

        parent::createForm($form);
    }

    /**
     * Creates a role distribution form container for the tab participant
     */
    protected function createRoleDistribution(TabFormContainer $tab, SingleSelectionFormField $mode): void
    {
        $roleDistributionContainer = FormContainer::create('roleDistribution')
            ->label('rp.event.raid.distribution.role');

        foreach (RoleHandler::getInstance()->getRoles() as $role) {
            $roleDistributionContainer->appendChild(
                IntegerFormField::create($role->identifier)
                    ->label($role->getTitle())
                    ->minimum(0)
                    ->value(0)
            );
        }

        $roleDistributionContainer->addDependency(
            ValueFormFieldDependency::create('roleSelect')
                ->field($mode)
                ->values(['role'])
        );

        $tab->appendChild($roleDistributionContainer);
    }

    #[\Override]
    public function getContent(): string
    {
        WCF::getTPL()->assign($this->getContentData());

        return WCF::getTPL()->render('rp', 'eventRaid', []);
    }

    #[\Override]
    public function getContentData(?string $key = null): mixed
    {
        if ($this->contentData === null) {
            $event = $this->getEvent();
            $this->contentData = [];

            $hasAttendee = 0;
            $attendees = [];

            $attendeeList = new EventRaidAttendeeList();
            $attendeeList->getConditionBuilder()->add('eventID = ?', [$event->getObjectID()]);
            $attendeeList->readObjects();

            /** @var EventRaidAttendee $attendee */
            foreach ($attendeeList as $attendee) {
                if ($attendee->getCharacter()->userID === WCF::getUser()->userID) {
                    $hasAttendee = $attendee->getObjectID();
                }

                $status = $attendee->status;
                $attendees[$status] ??= [];

                $key = match ($event->distributionMode) {
                    'class' => $attendee->classification,
                    'role' => $attendee->role,
                    'none' => 0,
                    default => 0,
                };

                $attendees[$status][$key] ??= [];
                $attendees[$status][$key][] = $attendee;
            }

            $availableDistributions = match ($event->distributionMode) {
                'class' => ClassificationHandler::getInstance()->getClassifications(),
                'role'  => RoleHandler::getInstance()->getRoles(),
                'none'  => ['' => WCF::getLanguage()->get('rp.event.raid.participants')],
                default => [],
            };

            // check users characters
            $characterEvent = new AvailableCharactersChecking(
                CharacterHandler::getInstance()->getCharacters(),
                $event
            );
            EventHandler::getInstance()->fire($characterEvent);

            $raidStatus = [];
            if ($this->isLeader()) {
                $raidStatus[EventRaidAttendee::STATUS_CONFIRMED] = WCF::getLanguage()->get('rp.event.raid.container.confirmed');
            }

            $raidStatus += [
                EventRaidAttendee::STATUS_LOGIN => WCF::getLanguage()->get('rp.event.raid.container.login'),
                EventRaidAttendee::STATUS_RESERVE => WCF::getLanguage()->get('rp.event.raid.container.reserve'),
                EventRaidAttendee::STATUS_LOGOUT => WCF::getLanguage()->get('rp.event.raid.container.logout'),
            ];

            $this->contentData = [
                'attendees' => $attendees,
                'availableCharacters' => $characterEvent->getAvailableCharacters(),
                'availableDistributions' => $availableDistributions,
                'availableRaidStatus' => [
                    EventRaidAttendee::STATUS_CONFIRMED => WCF::getLanguage()->get('rp.event.raid.container.confirmed'),
                    EventRaidAttendee::STATUS_LOGIN => WCF::getLanguage()->get('rp.event.raid.container.login'),
                    EventRaidAttendee::STATUS_RESERVE => WCF::getLanguage()->get('rp.event.raid.container.reserve'),
                    EventRaidAttendee::STATUS_LOGOUT => WCF::getLanguage()->get('rp.event.raid.container.logout'),
                ],
                'hasAttendee' => $hasAttendee,
                'raidStatus' => $raidStatus,
            ];
        }

        if ($key === null) {
            return $this->contentData;
        }

        return $this->contentData[$key] ?? null;
    }

    #[\Override]
    public function getContentHeaderNavigation(): string
    {
        $canParticipate = true;
        if ($this->getEvent()->isCanceled || $this->isExpired() || !WCF::getSession()->getPermission('user.rp.canParticipate')) $canParticipate = false;
        if (WCF::getUser()->userID && \count($this->getContentData('availableCharacters')) === 0) $canParticipate = false;

        return WCF::getTPL()->render(
            'rp',
            'eventRaidHeaderNavigation',
            [
                'canParticipate' => $canParticipate,
                'hasAttendee' => $this->getContentData('hasAttendee'),
                'isExpired' => $this->isExpired(),
            ]
        );
    }

    #[\Override]
    public function getIcon(?int $size = null): string
    {
        $raidEvent = (new RaidEventCache())->getCache()->getEvent($this->getEvent()->raidEventID);
        if ($raidEvent === null) return parent::getIcon($size);
        return $raidEvent->getIcon($size);
    }

    /**
     * Returns all character profiles from the raid leader of the current raid event.
     * @return  CharacterProfile[]
     */
    public function getLeaders(): array
    {
        return CharacterProfileRuntimeCache::getInstance()->getObjects($this->getEvent()->leaders);
    }

    /**
     * Returns an array of the requirements values.
     * 
     * Key is the language variable and value as integer.
     */
    public function getRequirements(): array
    {
        $requirements = [];
        $event = $this->getEvent();
        $game = GameHandler::getInstance()->getCurrentGame();

        switch ($event->distributionMode) {
            case 'class':
                foreach (ClassificationHandler::getInstance()->getClassifications() as $classification) {
                    $identifier = $classification->identifier;
                    $value = (int)$event->{$identifier};
                    if (!$value) {
                        continue;
                    }

                    $key = \sprintf(
                        'rp.classification.%s.%s',
                        $game->identifier,
                        $classification->identifier
                    );
                    $requirements[$key] = $value;
                }
                break;
            case 'none':
                $participants = $event->participants ?? null;
                if ($participants !== null) {
                    $requirements['rp.event.raid.participants'] = $participants;
                }
                break;
            case 'role':
                $roles = RoleHandler::getInstance()->getRoles();
                foreach ($roles as $role) {
                    $identifier = $role->identifier;
                    $value = (int)$event->{$identifier};
                    if (!$value) {
                        continue;
                    }

                    $key = \sprintf(
                        'rp.role.%s.%s',
                        $game->identifier,
                        $role->identifier
                    );
                    $requirements[$key] = $value;
                }
                break;
        }

        return $requirements;
    }

    #[\Override]
    public function getTitle(): string
    {
        $raidEvent = (new RaidEventCache())->getCache()->getEvent($this->getEvent()->raidEventID) ?? 'Unknown';
        return $raidEvent instanceof RaidEvent ? $raidEvent->getTitle() : $raidEvent;
    }

    #[\Override]
    public function isExpired(): bool
    {
        $event = $this->getEvent();
        return ($event->startTime - ($event->deadline * 3600)) < TIME_NOW;
    }

    /**
     * Returns is current user is leader of this raid event.
     */
    public function isLeader(): bool
    {
        $eventLeaders = $this->getEvent()->leaders;
        $characterIDs = \array_keys(CharacterHandler::getInstance()->getCharacters());

        return !empty(\array_intersect($characterIDs, $eventLeaders));
    }

    #[\Override]
    public function saveForm(array $formData): array
    {
        $formData['data']['leaders'] = $formData['leaders'] ?? [];
        unset($formData['leaders']);

        return parent::saveForm($formData);
    }
}
