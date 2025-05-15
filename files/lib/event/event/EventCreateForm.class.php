<?php

namespace rp\event\event;

use wcf\event\IPsr14Event;
use wcf\system\form\builder\IFormDocument;

/**
 * Indicates that a form is created in the Event Controller.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class EventCreateForm implements IPsr14Event
{
    public function __construct(
        public readonly IFormDocument $form,
        public readonly string $eventController
    ) {
    }
}
