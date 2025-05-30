<?php

namespace rp\page;

use CuyZ\Valinor\Mapper\MappingError;
use rp\data\raid\event\RaidEvent;
use rp\system\cache\eager\RaidEventCache;
use rp\system\listView\user\RaidListView;
use wcf\http\Helper;
use wcf\page\AbstractListViewPage;
use wcf\system\exception\IllegalLinkException;
use wcf\system\WCF;

/**
 * Shows the raid list page.
 *
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @extends AbstractListViewPage<RaidListView>
 */
final class RaidListPage extends AbstractListViewPage
{
    public ?RaidEvent $raidEvent = null;

    #[\Override]
    public function assignVariables(): void
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'raidEvent' => $this->raidEvent,
        ]);
    }

    #[\Override]
    protected function createListView(): RaidListView
    {
        return new RaidListView($this->raidEvent?->getObjectID() ?? 0);
    }

    #[\Override]
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
            $this->raidEvent = (new RaidEventCache())->getCache()->getEvent($parameters['raidEventID'] ?? 0);
        } catch (MappingError) {
            throw new IllegalLinkException();
        }
    }
}
