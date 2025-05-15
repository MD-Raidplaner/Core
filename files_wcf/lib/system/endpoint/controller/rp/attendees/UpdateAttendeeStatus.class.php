<?php

namespace wcf\system\endpoint\controller\rp\attendees;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use rp\data\event\raid\attendee\EventRaidAttendee;
use wcf\http\Helper;
use wcf\system\endpoint\IController;
use wcf\system\endpoint\PostRequest;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\WCF;

/**
 * API endpoint for the update of attendees.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
#[PostRequest('/rp/attendees/{id:\d+}/updateStatus')]
final class UpdateAttendeeStatus implements IController
{
    #[\Override]
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        $attendee = Helper::fetchObjectFromRequestParameter($variables['id'], EventRaidAttendee::class);

        $this->assertAttendeeIsEditable($attendee);

        $parameters = Helper::mapApiParameters($request, UpdateAttendeeStatusParameters::class);

        (new \rp\system\attendee\command\UpdateAttendeeStatus(
            $attendee,
            $attendee->getEvent()->distributionMode === 'role' ? $parameters->distributionId : 0,
            $parameters->status
        ))();

        return new JsonResponse([]);
    }

    private function assertAttendeeIsEditable(EventRaidAttendee $attendee): void
    {
        $attendeeUserID = $attendee->userID;
        $currentUser = WCF::getUser();
        $eventController = $attendee->getEvent()->getController();

        // Check if the attendee is a guest or if the user IDs are different
        if ($attendeeUserID === 0 || $attendeeUserID !== $currentUser->userID) {
            // Check if the current user is a leader of the event
            if (!$eventController->isLeader()) {
                throw new PermissionDeniedException();
            }
        }
    }
}

/** @internal */
final class UpdateAttendeeStatusParameters
{
    public function __construct(
        /** @var positive-int */
        public readonly int $distributionId,
        /** @var non-empty-string */
        public readonly string $status,
    ) {
    }
}
