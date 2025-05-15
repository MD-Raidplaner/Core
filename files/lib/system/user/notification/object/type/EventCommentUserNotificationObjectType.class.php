<?php

namespace rp\system\user\notification\object\type;

use rp\data\event\Event;
use wcf\data\comment\Comment;
use wcf\data\comment\CommentList;
use wcf\system\cache\runtime\UserProfileRuntimeCache;
use wcf\system\user\notification\object\CommentUserNotificationObject;
use wcf\system\user\notification\object\type\AbstractUserNotificationObjectType;
use wcf\system\user\notification\object\type\ICommentUserNotificationObjectType;
use wcf\system\user\notification\object\type\IMultiRecipientCommentUserNotificationObjectType;
use wcf\system\WCF;

/**
 * Represents a comment notification object type for comments on events.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class EventCommentUserNotificationObjectType extends AbstractUserNotificationObjectType implements
    ICommentUserNotificationObjectType,
    IMultiRecipientCommentUserNotificationObjectType
{
    /**
     * @inheritDoc
     */
    protected static $decoratorClassName = CommentUserNotificationObject::class;

    /**
     * @inheritDoc
     */
    protected static $objectClassName = Comment::class;

    /**
     * @inheritDoc
     */
    protected static $objectListClassName = CommentList::class;

    /**
     * @inheritDoc
     */
    public function getOwnerID($objectID): int
    {
        $sql = "SELECT      event.userID
                FROM        wcf1_comment comment
                INNER JOIN  rp1_event event
                ON          event.eventID = comment.objectID
                WHERE       comment.commentID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$objectID]);

        return $statement->fetchSingleColumn() ?: 0;
    }

    /**
     * @inheritDoc
     */
    public function getRecipientIDs(Comment $comment): array
    {
        $event = new Event($comment->objectID);
        \assert($event->eventID !== 0);

        $leaders = [];
        if ($event->isRaidEvent()) {
            $leaders = $event->leaders;
        }
        $users = UserProfileRuntimeCache::getInstance()->getObjects($leaders);

        // Add the event author to the recipients, to ensure, 
        // that he receive a notifications.
        $recipients = [$event->getUserID()];
        foreach ($users as $user) {
            if ($event->canRead($user)) {
                $recipients[] = $user->userID;
            }
        }

        return \array_unique($recipients);
    }
}
