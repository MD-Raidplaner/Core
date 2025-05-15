<?php

namespace rp\system\user\notification\event;

use wcf\data\user\UserProfile;
use wcf\system\comment\CommentHandler;

/**
 * Provides a default implementation of
 *  `TTestableCommentUserNotificationEvent::getTestCommentObjectData()`
 * used for event comment-related and event comment response-related user notification
 * events.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
trait TTestableEventCommentUserNotificationEvent
{
    use TTestableEventUserNotificationEvent;

    /**
     * @see TTestableCommentUserNotificationEvent::createTestComment()
     */
    protected static function getTestCommentObjectData(UserProfile $recipient, UserProfile $author)
    {
        return [
            'objectID' => self::getTestEvent($author)
                ->eventID,
            'objectTypeID' => CommentHandler::getInstance()->getObjectTypeID('de.md-raidplaner.rp.eventComment'),
        ];
    }
}
