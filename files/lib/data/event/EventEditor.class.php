<?php

namespace rp\data\event;

use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit events.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @mixin   Event
 * @extends DatabaseObjectEditor<Event>
 */
class EventEditor extends DatabaseObjectEditor
{
    protected static $baseClass = Event::class;
}
