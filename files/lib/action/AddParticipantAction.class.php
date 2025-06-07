<?php

namespace rp\action;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use rp\data\event\Event;
use rp\data\event\raid\attendee\EventRaidAttendee;
use rp\data\event\raid\attendee\EventRaidAttendeeAction;
use rp\system\cache\runtime\CharacterRuntimeCache;
use rp\system\cache\runtime\EventRuntimeCache;
use rp\system\character\AvailableCharacter;
use rp\system\classification\ClassificationHandler;
use rp\system\event\type\RaidEventType;
use rp\system\form\builder\field\DynamicSelectFormField;
use rp\system\race\RaceHandler;
use rp\system\role\RoleHandler;
use wcf\http\Helper;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\UserInputException;
use wcf\system\form\builder\field\EmailFormField;
use wcf\system\form\builder\field\SingleSelectionFormField;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\form\builder\Psr15DialogForm;
use wcf\system\WCF;

/**
 * Handles add participant
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class AddParticipantAction implements RequestHandlerInterface
{
    private ?Event $event = null;

    private function assertEventCanBeAdd(): void
    {
        if (!WCF::getSession()->getPermission('user.rp.canParticipate')) {
            throw new PermissionDeniedException();
        }

        if ($this->event->getType()->isExpired()) {
            throw new UserInputException('isExpired');
        }

        if (WCF::getUser()->userID) {
            if (!count($this->event->getType()->getContentData('availableCharacters'))) {
                throw new UserInputException('availableCharacters');
            }

            if ($this->event->getType()->getContentData('hasAttendee')) {
                throw new UserInputException('hasAttendee');
            }
        }
    }

    private function getForm(): Psr15DialogForm
    {
        $form = new Psr15DialogForm(
            static::class,
            WCF::getLanguage()->get('rp.event.raid.attendee.add')
        );

        if (WCF::getUser()->userID) {
            $availableCharacters = $this->event->getType()->getContentData('availableCharacters');
            $classificationRolesMap = ClassificationHandler::getInstance()->getRoleMapByClassification();

            $roleMapping = [];
            foreach ($availableCharacters as $characterID => $character) {
                $classification = $character->getClassification();

                if (isset($classificationRolesMap[$classification])) {
                    $roleMapping[$characterID] = $classificationRolesMap[$classification];
                }
            }

            $form->appendChildren([
                SingleSelectionFormField::create('characterID')
                    ->label('rp.event.raid.attendee.character')
                    ->required()
                    ->options($availableCharacters),
                DynamicSelectFormField::create('role')
                    ->label('rp.role.title')
                    ->required()
                    ->options(RoleHandler::getInstance()->getRoles())
                    ->triggerSelect('characterID')
                    ->optionsMapping($roleMapping),
                SingleSelectionFormField::create('status')
                    ->label('rp.event.raid.status')
                    ->required()
                    ->options($this->event->getType()->getContentData('raidStatus')),
            ]);
        } else {
            $form->appendChildren([
                TextFormField::create('characterName')
                    ->label('rp.event.raid.attendee.character')
                    ->required()
                    ->maximumLength(255),
                EmailFormField::create('email')
                    ->label('rp.event.raid.attendee.email')
                    ->required(),
                SingleSelectionFormField::create('raceID')
                    ->label('rp.race.title')
                    ->required()
                    ->options(['' => 'wcf.global.noSelection'] + RaceHandler::getInstance()->getRaces())
                    ->addValidator(new FormFieldValidator('check', function (SingleSelectionFormField $formField) {
                        $value = $formField->getSaveValue();

                        if (empty($value)) {
                            $formField->addValidationError(new FormFieldValidationError('empty'));
                        }
                    })),
                DynamicSelectFormField::create('classification')
                    ->label('rp.classification.title')
                    ->required()
                    ->options(ClassificationHandler::getInstance()->getClassifications())
                    ->triggerSelect(\sprintf('%s_%s', static::class, 'raceID'))
                    ->optionsMapping(ClassificationHandler::getInstance()->getRoleMapByClassification())
                    ->addValidator(new FormFieldValidator('check', function (SingleSelectionFormField $formField) {
                        $value = $formField->getSaveValue();

                        if (empty($value)) {
                            $formField->addValidationError(new FormFieldValidationError('empty'));
                        }
                    })),
                DynamicSelectFormField::create('role')
                    ->label('rp.role.title')
                    ->required()
                    ->options(RoleHandler::getInstance()->getRoles())
                    ->triggerSelect(\sprintf('%s_%s', static::class, 'classification'))
                    ->optionsMapping(ClassificationHandler::getInstance()->getRoleMapByClassification())
                    ->addValidator(new FormFieldValidator('check', function (SingleSelectionFormField $formField) {
                        $value = $formField->getSaveValue();

                        if (empty($value)) {
                            $formField->addValidationError(new FormFieldValidationError('empty'));
                        }
                    })),
            ]);
        }

        $form->appendChild(
            TextFormField::create('notes')
                ->label('rp.event.raid.attendee.notes')
                ->maximumLength(255)
        );

        $form->build();

        return $form;
    }

    #[\Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $parameters = Helper::mapQueryParameters(
            $request->getQueryParams(),
            <<<'EOT'
                array {
                    id: positive-int
                }
                EOT
        );

        $this->event = EventRuntimeCache::getInstance()->getObject($parameters['id']);
        if (!$this->event) {
            throw new IllegalLinkException();
        }

        $this->assertEventCanBeAdd();

        $form = $this->getForm();

        if ($request->getMethod() === 'GET') {
            return $form->toResponse();
        } elseif ($request->getMethod() === 'POST') {
            $response = $form->validateRequest($request);
            if ($response !== null) {
                return $response;
            }

            $formData = $form->getData()['data'];
            $attendeeData = [];
            if (WCF::getUser()->userID) {
                $availableCharacters = $this->event->getType()->getContentData('availableCharacters');

                /** @var AvailableCharacter $availableCharacter */
                $availableCharacter = $availableCharacters[$formData['characterID']];

                [$characterID] = \explode('_', $availableCharacter->getID(), 2);
                $character = CharacterRuntimeCache::getInstance()->getObject($characterID);

                $attendeeData = [
                    'characterID' => $character->characterID,
                    'characterName' => $character->characterName,
                    'classification' => $availableCharacter->getClassification(),
                    'internID' => $availableCharacter->getID(),
                    'role' => $formData['role'],
                    'status' => $formData['status'],
                ];
            } else {
                $attendeeData = [
                    'characterName' => $formData['characterName'],
                    'email' => $formData['email'],
                    'classification' => $formData['classification'],
                    'role' => $formData['role'],
                    'status' => EventRaidAttendee::STATUS_LOGIN,
                ];
            }

            $attendeeData['eventID'] = $this->event->eventID;
            $attendeeData['notes'] = $formData['notes'];

            $attendee = (new EventRaidAttendeeAction([], 'create', ['data' => $attendeeData]))->executeAction()['returnValues'];

            $distribution = '';
            switch ($this->event->distributionMode) {
                case 'class':
                    $distribution = $attendee->classification;
                    break;
                case 'role':
                    $distribution = $attendee->role;
                    break;
            }

            return new JsonResponse([
                'result' => [
                    'attendeeId' => $attendee->attendeeID,
                    'distribution' => $distribution,
                    'status' => $attendee->status,
                ],
            ]);
        } else {
            throw new \LogicException('Unreachable');
        }
    }
}
