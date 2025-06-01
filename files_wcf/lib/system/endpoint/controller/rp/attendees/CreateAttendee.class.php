<?php

namespace wcf\system\endpoint\controller\rp\attendees;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use rp\data\event\Event;
use rp\data\event\raid\attendee\EventRaidAttendeeList;
use rp\event\character\AvailableCharactersChecking;
use rp\system\cache\runtime\CharacterRuntimeCache;
use rp\system\character\AvailableCharacter;
use rp\system\character\CharacterHandler;
use wcf\data\object\type\ObjectTypeCache;
use wcf\http\Helper;
use wcf\system\endpoint\IController;
use wcf\system\endpoint\PostRequest;
use wcf\system\event\EventHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\WCF;

/**
 * API endpoint for the creation of new attendees.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
#[PostRequest('/rp/attendees')]
final class CreateAttendee implements IController
{
    #[\Override]
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        $parameters = Helper::mapApiParameters($request, CreateAttendeeParameters::class);
        $event = Helper::fetchObjectFromRequestParameter($parameters->eventID, Event::class);


        $this->assertAttendeeIsPossible($event);
        $availableCharacter = $this->getAvailableCharacter($event, $parameters->characterID);

        [$characterID] = \explode('_', $availableCharacter->getID(), 2);
        $character = CharacterRuntimeCache::getInstance()->getObject($characterID);

        $attendee = (new \rp\system\attendee\command\CreateAttendee(
            $event,
            $character->characterID,
            $character->characterName,
            $availableCharacter->getClassificationID(),
            $availableCharacter->getID(),
            $parameters->role,
            $parameters->status,
        ))();

        return new JsonResponse([
            'attendeeId' => $attendee->attendeeID,
        ]);
    }

    private function assertAttendeeIsPossible(Event $event): void
    {
        $objectTypeID = ObjectTypeCache::getInstance()->getObjectTypeIDByName('de.md-raidplaner.rp.event.controller', 'de.md-raidplaner.rp.event.controller.raid');
        if ($objectTypeID !== $event->objectTypeID) {
            throw new IllegalLinkException();
        }

        if (
            $event->isCanceled ||
            $event->isDeleted ||
            $event->startTime < TIME_NOW
        ) {
            throw new PermissionDeniedException();
        }

        $characterIDs = \array_column(CharacterHandler::getInstance()->getCharacters(), 'characterID');
        $attendeeList = new EventRaidAttendeeList();
        $attendeeList->getConditionBuilder()->add('eventID = ?', [$event->eventID]);
        $attendeeList->getConditionBuilder()->add('characterID IN (?)', [$characterIDs]);
        if ($attendeeList->countObjects() > 0) {
            throw new PermissionDeniedException();
        }
    }

    private function getAvailableCharacter(Event $event, int|string $characterID): AvailableCharacter
    {
        $event = new AvailableCharactersChecking(
            CharacterHandler::getInstance()->getCharacters(),
            $event
        );
        EventHandler::getInstance()->fire($event);

        $availableCharacters = $event->getAvailableCharacters();

        if (!isset($availableCharacters[$characterID])) {
            throw new IllegalLinkException();
        }

        return $availableCharacters[$characterID];
    }
}

/** @internal */
final class CreateAttendeeParameters
{
    public function __construct(
        /** @var positive-int **/
        public readonly int $eventID,

        /** @var non-empty-string */
        public readonly string $characterID,

        public readonly string $role,

        public readonly int $status,

        public readonly string $guestToken,
    ) {
    }
}
