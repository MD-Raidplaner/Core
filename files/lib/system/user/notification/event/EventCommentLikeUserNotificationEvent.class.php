<?php

namespace rp\system\user\notification\event;

use rp\system\cache\runtime\ViewableEventRuntimeCache;
use wcf\system\user\notification\event\AbstractSharedUserNotificationEvent;
use wcf\system\user\notification\event\ITestableUserNotificationEvent;
use wcf\system\user\notification\event\TReactionUserNotificationEvent;
use wcf\system\user\notification\event\TTestableCommentLikeUserNotificationEvent;
use wcf\system\user\notification\object\LikeUserNotificationObject;

/**
 * User notification event for event comment likes.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @method  LikeUserNotificationObject  getUserNotificationObject()
 */
final class EventCommentLikeUserNotificationEvent extends AbstractSharedUserNotificationEvent implements 
ITestableUserNotificationEvent
{
    use TTestableCommentLikeUserNotificationEvent;
    use TTestableEventCommentUserNotificationEvent;
    use TReactionUserNotificationEvent;

    protected $stackable = true;

    /**
     * Returns the liked comment's id.
     */
    protected function getCommentID(): int
    {
        // this is the `wcfN_like.objectID` value
        return $this->getUserNotificationObject()->objectID;
    }

    #[\Override]
    public function getEmailMessage($notificationType = 'instant'): void
    {
        // not supported
    }

    #[\Override]
    public function getEventHash(): string
    {
        return \sha1($this->eventID . '-' . $this->getCommentID());
    }

    #[\Override]
    public function getLink(): string
    {
        return ViewableEventRuntimeCache::getInstance()->getObject($this->additionalData['objectID'])->getLink() . '#comment' . $this->getCommentID();
    }

    #[\Override]
    public function getMessage(): string
    {
        $event = ViewableEventRuntimeCache::getInstance()->getObject($this->additionalData['objectID']);
        $authors = \array_values($this->getAuthors());
        $count = \count($authors);

        if ($count > 1) {
            return $this->getLanguage()->getDynamicVariable('rp.user.notification.eventComment.like.message.stacked', [
                'author' => $this->author,
                'authors' => $authors,
                'commentID' => $this->getCommentID(),
                'count' => $count,
                'event' => $event,
                'others' => $count - 1,
                'reactions' => $this->getReactionsForAuthors(),
            ]);
        }

        return $this->getLanguage()->getDynamicVariable('rp.user.notification.eventComment.like.message', [
            'author' => $this->author,
            'commentID' => $this->getCommentID(),
            'event' => $event,
            'reactions' => $this->getReactionsForAuthors(),
        ]);
    }

    #[\Override]
    public function getTitle(): string
    {
        $count = \count($this->getAuthors());
        if ($count > 1) {
            return $this->getLanguage()->getDynamicVariable('rp.user.notification.eventComment.like.title.stacked', [
                'count' => $count,
                'timesTriggered' => $this->notification->timesTriggered,
            ]);
        }

        return $this->getLanguage()->get('rp.user.notification.eventComment.like.title');
    }

    #[\Override]
    protected function prepare(): void
    {
        ViewableEventRuntimeCache::getInstance()->cacheObjectID($this->additionalData['objectID']);
    }

    #[\Override]
    public function supportsEmailNotification(): bool
    {
        return false;
    }
}
