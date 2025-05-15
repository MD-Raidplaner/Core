<?php

namespace rp\system\event\discussion;

use rp\data\event\Event;

/**
 * Default implementation for discussion provider for events. Any actual implementation
 * should derive from this class for forwards-compatibility.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
abstract class AbstractEventDiscussionProvider implements IEventDiscussionProvider
{
    /**
     * event object
     */
    protected Event $event;

    /**
     * AbstractEventDiscussionProvider constructor.
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
    }
}
