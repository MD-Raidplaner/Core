<?php

namespace rp\system\calendar;

use rp\data\event\AccessibleEventList;
use rp\data\event\ViewableEvent;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\DateUtil;

/**
 * Represents a calendar for a specific month and year, 
 * allowing users to add events and render the calendar.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class Calendar
{
    /**
     * calendar days
     */
    private array $calendarDays  = [];

    /**
     * events
     */
    private array $events = [];

    /**
     * the first day of the last month
     */
    private \DateTimeImmutable $firstDayOfLastMonth;

    /**
     * the first day of the current month.
     */
    private \DateTimeImmutable $firstDayOfMonth;

    /**
     * the first day of the next month.
     */
    private \DateTimeImmutable $firstDayOfNextMonth;

    /**
     * the localized name of the month.
     */
    private string $monthName;

    /**
     * Constructor to initialize the Calendar object with the given year and month.
     */
    public function __construct(
        private readonly int $year,
        private readonly int $month
    ) {
        $this->firstDayOfMonth = new \DateTimeImmutable("{$year}-{$month}-01");
        $this->monthName = DateUtil::localizeDate($this->firstDayOfMonth->format('F'), 'F', WCF::getLanguage());

        $this->firstDayOfLastMonth =  $this->firstDayOfMonth->modify('-1 month');
        $this->firstDayOfNextMonth = $this->firstDayOfMonth->modify('+1 month');

        $this->initEventDays();
    }

    /**
     * Assigns events to specific days in the calendar.
     */
    public function calculate(AccessibleEventList $eventList): void
    {
        $events = $fullDayEvents = [];

        foreach ($eventList as $event) {
            if ($event->isFullDay) {
                $fullDayEvents[] = $event;
            } else {
                $events[] = $event;
            }
        }

        $this->sortFullDayEvents($fullDayEvents);
        $this->sortEvents($events);

        foreach ([...$fullDayEvents, ...$events] as $event) {
            $eventDays = $event->getEventDays($this->month);

            foreach ($eventDays as $eventDay) {
                if (!isset($this->calendarDays[$eventDay])) continue;
                $day = $this->calendarDays[$eventDay];
                $dayEvent = new DayEvent($day, $event);

                if (\count($eventDays) > 1 && !$event->isFullDay) {
                    if ($day->__toString() === \reset($eventDays)) {
                        $dayEvent->setStatus(DayEvent::EVENT_STATUS_START);
                    } else if ($day->__toString() === \end($eventDays)) {
                        $dayEvent->setStatus(DayEvent::EVENT_STATUS_END);
                    } else {
                        $dayEvent->setStatus(DayEvent::EVENT_STATUS_MIDDLE);
                    }
                }
                $day->addEvent($dayEvent);
            }
        }
    }

    public function calculateEvent(DayEvent $dayEvent, int &$index, array &$events): void
    {
        $day = $dayEvent->getDay()->__toString();

        if ($index > 0) {
            $i = 0;
            do {
                if (!isset($events[$day][$i])) {
                    $events[$day][$i] = null;
                }
                $i++;
            } while ($i < $index);
        }

        if (
            !isset($events[$day][$index])
            || $events[$day][$index] === null
        ) {
            $events[$day][$index] = $dayEvent;
        } else {
            ++$index;
            $this->calculateEvent($dayEvent, $index, $events);
        }
    }

    /**
     * Returns the timestamp for the last day of the month.
     */
    public function getEndTimestamp(): int
    {
        $lastDay = $this->firstDayOfNextMonth
            ->modify('-1 day')
            ->setTime(23, 59, 59);
        return $lastDay->getTimestamp();
    }

    /**
     * Returns the link to the last month.
     */
    public function getLastMonthLink(): string
    {
        return LinkHandler::getInstance()->getLink('Calendar', [
            'application' => 'rp',
            'month' => $this->firstDayOfLastMonth->format('n'),
            'year' => $this->firstDayOfLastMonth->format('Y'),
        ]);
    }

    /**
     * Returns the link to the next month.
     */
    public function getNextMonthLink(): string
    {
        return LinkHandler::getInstance()->getLink('Calendar', [
            'application' => 'rp',
            'month' => $this->firstDayOfNextMonth->format('n'),
            'year' => $this->firstDayOfNextMonth->format('Y'),
        ]);
    }

    /**
     * Returns the timestamp for the first day of the month.
     */
    public function getStartTimestamp(): int
    {
        return $this->firstDayOfMonth->setTime(0, 0, 0)->getTimestamp();
    }

    public function getTemplate(): string
    {
        return WCF::getTPL()->fetch('renderCalendar', 'rp', [
            'days' => $this->calendarDays,
            'monthName' => $this->monthName,
            'weekDays' => DateUtil::getWeekDays(),
            'year' => $this->year,
        ], true);
    }

    private function initEventDays(): void
    {
        $weekDays = DateUtil::getWeekDays();
        $firstDayOfWeek = (int)$this->firstDayOfMonth->format('N');
        $monthDays = $this->firstDayOfMonth->format('t');

        foreach ($weekDays as $key => $weekDay) {
            if ($key === $firstDayOfWeek) break;
            $this->calendarDays[] = null;
        }

        for ($i = 1; $i <= $monthDays; $i++) {
            $day = new Day($this->year, $this->month, $i);
            $this->calendarDays[$day->__toString()] = $day;
        }

        $lastDayOfWeek = new \DateTimeImmutable("{$this->year}-{$this->month}-{$this->firstDayOfMonth->format('t')}");
        $lastDayOfWeek = (int)$lastDayOfWeek->format('N');
        $isLastDay = false;
        foreach ($weekDays as $key => $weekDay) {
            if ($key === $lastDayOfWeek) {
                $isLastDay = true;
                continue;
            }

            if ($isLastDay) {
                $this->calendarDays[] = null;
            }
        }
    }

    /**
     * Sort the events by start time
     */
    private function sortEvents(&$events): void
    {
        \usort($events, static function (ViewableEvent $a, ViewableEvent $b) {
            return $a->startTime <=> $b->startTime;
        });
    }

    /**
     * Sort the full day events by title
     */
    private function sortFullDayEvents(&$fullDayEvents): void
    {
        \usort($fullDayEvents, static function (ViewableEvent $a, ViewableEvent $b) {
            return $a->getTitle() <=> $b->getTitle();
        });
    }
}
