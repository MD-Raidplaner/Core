<?php

namespace rp\page;

use rp\data\raid\event\RaidEvent;
use rp\data\raid\event\RaidEventCache;
use rp\data\raid\RaidList;
use wcf\http\Helper;
use wcf\page\MultipleLinkPage;
use wcf\system\WCF;

/**
 * Shows the raids page.
 *
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class RaidListPage extends MultipleLinkPage
{
    /**
     * @inheritDoc
     */
    public $itemsPerPage = 60;

    /**
     * @inheritDoc
     */
    public $objectListClassName = RaidList::class;

    /**
     * raid event object
     */
    public ?RaidEvent $raidEvent = null;

    /**
     * @inheritDoc
     */
    public $sortField = 'time';

    /**
     * @inheritDoc
     */
    public $sortOrder = 'DESC';

    /**
     * @inheritDoc
     */
    public function assignVariables(): void
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'raidEvent' => $this->raidEvent,
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function initObjectList(): void
    {
        parent::initObjectList();

        if ($this->raidEvent) {
            $this->objectList->getConditionBuilder()->add('raidEventID = ?', [$this->raidEvent->eventID]);
        }
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
                        raidEventID?: positive-int
                    }
                    EOT
            );
            $raidEventID = $parameters['raidEventID'] ?? 0;
            if ($raidEventID) {
                $this->raidEvent = RaidEventCache::getInstance()->getEventByID($raidEventID);
            }
        } catch (MappingError) {
            throw new IllegalLinkException();
        }
    }
}
