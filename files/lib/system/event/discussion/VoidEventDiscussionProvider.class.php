<?php

namespace rp\system\event\discussion;

use rp\data\event\Event;

/**
 * Represents a non-existing discussion provider and is used when there is no other
 * type of discussion being available. This provider is always being evaluated last.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class VoidEventDiscussionProvider extends AbstractEventDiscussionProvider
{
    #[\Override]
    public function getDiscussionCount(): int
    {
        return 0;
    }

    #[\Override]
    public function getDiscussionCountPhrase(): string
    {
        return '';
    }

    #[\Override]
    public function getDiscussionLink(): string
    {
        return '';
    }

    #[\Override]
    public static function isResponsible(Event $event): bool
    {
        return true;
    }

    #[\Override]
    public function renderDiscussions(): string
    {
        return '';
    }
}
