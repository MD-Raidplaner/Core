<?php

namespace rp\system\event\discussion;

use rp\data\event\Event;

/**
 * Discussion provider for events.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
interface IEventDiscussionProvider
{
    /**
     * Returns the number of discussion items.
     */
    public function getDiscussionCount(): int;

    /**
     * Returns the simple phrase "X <discussions>" that is used for both the statistics
     * and the meta data in the event's headline.
     */
    public function getDiscussionCountPhrase(): string;

    /**
     * Returns the permalink to the discussions or an empty string if there is none.
     */
    public function getDiscussionLink(): string;

    /**
     * Returning true will assign this provider to the event, otherwise the next
     * possible provider is being evaluated.
     */
    public static function isResponsible(Event $event): bool;

    /**
     * Renders the input and display section of the associated discussion.
     */
    public function renderDiscussions(): string;
}
