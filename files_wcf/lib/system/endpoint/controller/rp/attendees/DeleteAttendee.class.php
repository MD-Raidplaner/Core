<?php

namespace wcf\system\endpoint\controller\rp\attendees;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use rp\data\event\raid\attendee\EventRaidAttendee;
use wcf\http\Helper;
use wcf\system\endpoint\DeleteRequest;
use wcf\system\endpoint\IController;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\WCF;

/**
 * API endpoint for the deletion of attendees.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
#[DeleteRequest('/rp/attendees/{id:\d+}')]
final class DeleteAttendee implements IController
{
    #[\Override]
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        $attendee = Helper::fetchObjectFromRequestParameter($variables['id'], EventRaidAttendee::class);

        $this->assertAttendeeIsDeletable($attendee);

        (new \rp\system\attendee\command\DeleteAttendees([$attendee]))();

        return new JsonResponse([]);
    }

    private function assertAttendeeIsDeletable(EventRaidAttendee $attendee): void
    {
        if ($attendee->getCharacter()->userID !== WCF::getUser()->userID) {
            throw new PermissionDeniedException();
        }
    }
}
