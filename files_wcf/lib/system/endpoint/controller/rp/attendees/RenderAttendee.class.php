<?php

namespace wcf\system\endpoint\controller\rp\attendees;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use rp\data\event\raid\attendee\EventRaidAttendee;
use wcf\data\template\Template;
use wcf\http\Helper;
use wcf\system\endpoint\GetRequest;
use wcf\system\endpoint\IController;
use wcf\system\WCF;

/**
 * API endpoint for loading rendered attendees.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
#[GetRequest('/rp/attendees/render')]
final class RenderAttendee implements IController
{
    #[\Override]
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        $parameters = Helper::mapApiParameters($request, RenderAttendeeParameters::class);
        $attendee = Helper::fetchObjectFromRequestParameter($parameters->attendeeID, EventRaidAttendee::class);

        $distribution = '';
        switch ($attendee->getEvent()->distributionMode) {
            case 'class':
                $distribution = $attendee->classification;
                break;
            case 'role':
                $distribution = $attendee->role;
                break;
        }

        return new JsonResponse([
            'distribution' => $distribution,
            'template' => WCF::getTPL()->render(
                'rp',
                'eventRaidAttendeeItems',
                [
                    'attendee' => $attendee,
                    'event' => $attendee->getEvent(),
                    '__availableDistribution' => $distribution,
                ]
            ),
        ]);
    }
}

/** @internal */
final class RenderAttendeeParameters
{
    public function __construct(
        /** @var positive-int **/
        public readonly int $attendeeID,
    ) {}
}
