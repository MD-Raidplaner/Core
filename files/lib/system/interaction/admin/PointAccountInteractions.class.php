<?php

namespace rp\system\interaction\admin;

use rp\data\point\account\PointAccount;
use rp\event\interaction\admin\PointAccountInteractionCollecting;
use wcf\system\event\EventHandler;
use wcf\system\interaction\AbstractInteractionProvider;
use wcf\system\interaction\DeleteInteraction;

/**
 * Interaction provider for point accounts.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class PointAccountInteractions extends AbstractInteractionProvider
{
    public function __construct()
    {
        $this->addInteractions([
            new DeleteInteraction("rp/point/accounts/%s")
        ]);

        EventHandler::getInstance()->fire(
            new PointAccountInteractionCollecting($this)
        );
    }

    #[\Override]
    public function getObjectClassName(): string
    {
        return PointAccount::class;
    }
}
