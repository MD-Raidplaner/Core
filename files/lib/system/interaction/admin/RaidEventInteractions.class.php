<?php

namespace rp\system\interaction\admin;

use rp\data\raid\event\RaidEvent;
use rp\event\interaction\admin\RaidEventInteractionCollecting;
use wcf\system\event\EventHandler;
use wcf\system\interaction\AbstractInteractionProvider;
use wcf\system\interaction\DeleteInteraction;

/**
 * Interaction provider for raid events.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class RaidEventInteractions extends AbstractInteractionProvider
{
    public function __construct()
    {
        $this->addInteractions([
            new DeleteInteraction("rp/raid/events/%s")
        ]);

        EventHandler::getInstance()->fire(
            new RaidEventInteractionCollecting($this)
        );
    }

    #[\Override]
    public function getObjectClassName(): string
    {
        return RaidEvent::class;
    }
}
