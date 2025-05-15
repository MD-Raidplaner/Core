<?php

namespace rp\page;

use CuyZ\Valinor\Mapper\MappingError;
use rp\data\event\AccessibleEventList;
use rp\data\event\EventAction;
use rp\data\event\EventEditor;
use rp\data\event\ViewableEvent;
use wcf\http\Helper;
use wcf\page\AbstractPage;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\MetaTagHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows the event page.
 *
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class EventPage extends AbstractPage
{
    /**
     * event object
     */
    public ?ViewableEvent $event;
    /**
     * event id
     */
    public int $eventID = 0;

    /**
     * next event
     */
    public ?ViewableEvent $nextEvent = null;

    /**
     * previous event
     */
    public ?ViewableEvent $previousEvent = null;

    /**
     * @inheritDoc
     */
    public function assignVariables(): void
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'event' => $this->event,
            'eventID' => $this->eventID,
            'nextEvent' => $this->nextEvent,
            'previousEvent' => $this->previousEvent,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function checkPermissions(): void
    {
        parent::checkPermissions();

        $this->event->getController()->checkPermissions();
    }

    /**
     * @inheritDoc
     */
    public function readData(): void
    {
        parent::readData();

        // update view count
        $eventEditor = new EventEditor($this->event->getDecoratedObject());
        $eventEditor->updateCounters([
            'views' => 1,
        ]);

        // update event visit
        if ($this->event->isNew()) {
            $eventAction = new EventAction([$this->event->getDecoratedObject()], 'markAsRead', [
                'viewableEvent' => $this->event
            ]);
            $eventAction->executeAction();
        }

        // get next event
        $eventList = new AccessibleEventList();
        $eventList->getConditionBuilder()->add('event.startTime > ?', [$this->event->startTime]);
        $eventList->sqlOrderBy = 'event.startTime ASC';
        $eventList->sqlLimit = 1;
        $eventList->readObjects();
        foreach ($eventList as $event) {
            $this->nextEvent = $event;
        }

        // get previous event
        $eventList = new AccessibleEventList();
        $eventList->getConditionBuilder()->add('event.startTime < ?', [$this->event->startTime]);
        $eventList->sqlOrderBy = 'event.startTime DESC';
        $eventList->sqlLimit = 1;
        $eventList->readObjects();
        foreach ($eventList as $event) {
            $this->previousEvent = $event;
        }

        $endDateTime = new \DateTimeImmutable('@' . $this->event->endTime, !$this->event->isFullDay ? WCF::getUser()->getTimeZone() : null);
        $startDateTime = new \DateTimeImmutable('@' . $this->event->startTime, !$this->event->isFullDay ? WCF::getUser()->getTimeZone() : null);

        // add meta/og tags
        MetaTagHandler::getInstance()->addTag('og:type', 'og:type', 'event', true);
        MetaTagHandler::getInstance()->addTag('og:title', 'og:title', $this->event->getTitle() . ' - ' . WCF::getLanguage()->get(\PAGE_TITLE), true);
        MetaTagHandler::getInstance()->addTag('og:description', 'og:description', StringUtil::decodeHTML(StringUtil::stripHTML($this->event->getExcerpt())), true);
        MetaTagHandler::getInstance()->addTag('og:url', 'og:url', $this->event->getLink(), true);
        MetaTagHandler::getInstance()->addTag('og:start_time', 'og:start_time', $startDateTime->format('c'), true);
        MetaTagHandler::getInstance()->addTag('og:end_time', 'og:end_time', $endDateTime->format('c'), true);
    }

    /**
     * @inheritDoc
     */
    public function readParameters(): void
    {
        parent::readParameters();

        try {
            $parameters = Helper::mapQueryParameters(
                $_GET,
                <<<'EOT'
            array {
                id?: positive-int
            }
            EOT
            );
            $this->eventID = $parameters['id'] ?? 0;
        } catch (MappingError) {
            throw new IllegalLinkException();
        }

        $this->event = ViewableEvent::getEvent($this->eventID);
        if ($this->event === null) {
            throw new IllegalLinkException();
        }

        $this->canonicalURL = $this->event->getLink();
    }
}
