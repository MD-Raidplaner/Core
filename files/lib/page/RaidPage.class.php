<?php

namespace rp\page;

use CuyZ\Valinor\Mapper\MappingError;
use rp\data\raid\Raid;
use rp\system\cache\runtime\RaidRuntimeCache;
use rp\system\classification\ClassificationHandler;
use wcf\http\Helper;
use wcf\page\AbstractPage;
use wcf\system\exception\IllegalLinkException;
use wcf\system\WCF;

/**
 * Shows the raid page.
 *
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class RaidPage extends AbstractPage
{
    public array $classDistributions = [];
    public Raid $raid;

    #[\Override]
    public function assignVariables(): void
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'classDistributions' => $this->classDistributions,
            'raid' => $this->raid
        ]);
    }

    #[\Override]
    public function readData(): void
    {
        parent::readData();

        $attendees = $this->raid->getAttendees();
        $classDistributions = [];
        foreach ($attendees as $attendee) {
            $classification = ClassificationHandler::getInstance()->getClassificationByIdentifier($attendee['classification'] ?? '');
            if ($classification === null) {
                continue;
            }

            if (!isset($classDistributions[$classification])) {
                $classDistributions[$classification] = [
                    'attendees' => [],
                    'count' => 0,
                    'object' => $classification,
                    'percent' => 0,
                ];
            }

            $classDistributions[$classification]['count']++;
            $classDistributions[$classification]['attendees'][] = $attendee;
        }

        $totalAttendees = \count($attendees);
        foreach ($classDistributions as $classification => $distribution) {
            $classDistributions[$classification]['percent'] = $totalAttendees > 0
                ? \number_format(($distribution['count'] / $totalAttendees) * 100)
                : 0;
        }

        $this->classDistributions = $classDistributions;
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
                        id: positive-int
                    }
                    EOT
            );

            $this->raid = RaidRuntimeCache::getInstance()->getObject($parameters['id']);
            if ($this->raid === null) {
                throw new IllegalLinkException();
            }
        } catch (MappingError) {
            throw new IllegalLinkException();
        }

        $this->canonicalURL = $this->raid->getLink();
    }
}
