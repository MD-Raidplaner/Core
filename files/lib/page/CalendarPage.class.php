<?php

namespace rp\page;

use CuyZ\Valinor\Mapper\MappingError;
use rp\data\event\AccessibleEventList;
use rp\system\calendar\Calendar;
use wcf\data\object\type\ObjectType;
use wcf\data\object\type\ObjectTypeCache;
use wcf\http\Helper;
use wcf\page\AbstractPage;
use wcf\system\exception\IllegalLinkException;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\DateUtil;
use wcf\util\HeaderUtil;

/**
 * Shows the calendar page.
 *
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
final class CalendarPage extends AbstractPage
{
    /**
     * calendar object
     */
    public Calendar $calendar;

    /**
     * link current day
     */
    public string $currentLink;

    /**
     * @inheritDoc
     */
    public function assignVariables(): void
    {
        parent::assignVariables();

        $eventControllers = \array_filter(
            ObjectTypeCache::getInstance()->getObjectTypes('de.md-raidplaner.rp.event.controller'),
            fn($controller) => $controller->getProcessor()->isAccessible() &&
                (RP_CURRENT_GAME_ID != 1 || $controller->objectType !== 'de.md-raidplaner.rp.event.controller.raid')
        );

        \uasort($eventControllers, function (ObjectType $a, ObjectType $b) {
            return \strcmp(
                WCF::getLanguage()->get('rp.event.controller.' . $a->objectType),
                WCF::getLanguage()->get('rp.event.controller.' . $b->objectType)
            );
        });

        WCF::getTPL()->assign([
            'calendar' => $this->calendar->getTemplate(),
            'currentLink' => $this->currentLink,
            'eventControllers' => $eventControllers,
            'lastMonthLink' => $this->calendar->getLastMonthLink(),
            'nextMonthLink' => $this->calendar->getNextMonthLink(),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function readData(): void
    {
        $currentDate = new \DateTimeImmutable('now');
        $this->currentLink = LinkHandler::getInstance()->getLink('Calendar', [
            'application' => 'rp',
            'month' => $currentDate->format('n'),
            'year' => $currentDate->format('Y'),
        ]);

        $eventList = new AccessibleEventList(
            $this->calendar->getStartTimestamp(),
            $this->calendar->getEndTimestamp()
        );
        $eventList->readObjects();
        $this->calendar->calculate($eventList);
    }

    /**
     * @inheritDoc
     */
    public function readParameters(): void
    {
        parent::readParameters();

        try {
            $parameters = Helper::mapQueryParameters(
                $_POST,
                <<<'EOT'
                    array {
                        objectType?: string
                    }
                    EOT
            );

            if (isset($parameters['objectType'])) {
                HeaderUtil::redirect(
                    LinkHandler::getInstance()->getLink(
                        'EventAdd',
                        [
                            'application' => 'rp',
                            'type' => $parameters['objectType']
                        ]
                    )
                );
                exit;
            }

            $parameters = Helper::mapQueryParameters(
                $_GET,
                <<<'EOT'
                    array {
                        month?: positive-int,
                        year?: positive-int
                    }
                    EOT
            );

            $month = $parameters['month'] ?? DateUtil::format(DateUtil::getDateTimeByTimestamp(TIME_NOW), 'n');
            $year = $parameters['year'] ?? DateUtil::format(DateUtil::getDateTimeByTimestamp(TIME_NOW), 'Y');

            if ($month < 1 || $month > 12) throw new IllegalLinkException();
        } catch (MappingError) {
            throw new IllegalLinkException();
        }

        $this->calendar = new Calendar($year, $month);
    }
}
