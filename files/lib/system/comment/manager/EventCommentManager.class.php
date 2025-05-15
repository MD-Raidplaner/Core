<?php

namespace rp\system\comment\manager;

use rp\data\event\Event;
use rp\data\event\EventEditor;
use rp\data\event\EventList;
use rp\system\cache\runtime\ViewableEventRuntimeCache;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\user\UserProfile;
use wcf\system\cache\runtime\UserProfileRuntimeCache;
use wcf\system\cache\runtime\ViewableCommentResponseRuntimeCache;
use wcf\system\cache\runtime\ViewableCommentRuntimeCache;
use wcf\system\comment\manager\AbstractCommentManager;
use wcf\system\comment\manager\ICommentPermissionManager;
use wcf\system\like\IViewableLikeProvider;
use wcf\system\WCF;

/**
 * Default implementation for discussion provider for events. Any actual implementation
 * should derive from this class for forwards-compatibility.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class EventCommentManager extends AbstractCommentManager implements IViewableLikeProvider, ICommentPermissionManager
{
    /**
     * @inheritDoc
     */
    protected $permissionAdd = 'user.rp.canAddEventComment';

    /**
     * @inheritDoc
     */
    protected $permissionAddWithoutModeration = 'user.rp.canAddEventCommentWithoutModeration';

    /**
     * @inheritDoc
     */
    protected $permissionCanModerate = 'mod.rp.canModerateEventComment';

    /**
     * @inheritDoc
     */
    protected $permissionDelete = 'user.rp.canDeleteEventComment';

    /**
     * @inheritDoc
     */
    protected $permissionEdit = 'user.rp.canEditEventComment';

    /**
     * @inheritDoc
     */
    protected $permissionModDelete = 'mod.rp.canDeleteEventComment';

    /**
     * @inheritDoc
     */
    protected $permissionModEdit = 'mod.rp.canEditEventComment';

    #[\Override]
    public function canModerateObject(int $objectTypeID, int $objectID, UserProfile $user): bool
    {
        $event = ViewableEventRuntimeCache::getInstance()->getObject($objectID);
        if (!$event->eventID || !$event->canRead($user)) {
            return false;
        }

        return (bool)$user->getPermission($this->permissionCanModerate);
    }

    /**
     * @inheritDoc
     */
    public function getLink($objectTypeID, $objectID): string
    {
        $event = ViewableEventRuntimeCache::getInstance()->getObject($objectID);
        if ($event) {
            return $event->getLink();
        }

        return '';
    }

    /**
     * @inheritDoc
     */
    public function getTitle($objectTypeID, $objectID, $isResponse = false): string
    {
        if ($isResponse) {
            return WCF::getLanguage()->get('rp.event.commentResponse');
        }

        return WCF::getLanguage()->getDynamicVariable('rp.event.comment');
    }

    /**
     * @inheritDoc
     */
    public function isAccessible($objectID, $validateWritePermission = false): bool
    {
        // check object id
        $event = new Event($objectID);
        if ($event === null || !$event->canRead()) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function isContentAuthor($commentOrResponse): bool
    {
        $event = ViewableEventRuntimeCache::getInstance()->getObject($this->getObjectID($commentOrResponse));

        return $commentOrResponse->userID && $event->userID == $commentOrResponse->userID;
    }

    /**
     * @inheritDoc
     */
    public function prepare(array $likes): void
    {
        $commentLikeObjectType = ObjectTypeCache::getInstance()
            ->getObjectTypeByName('com.woltlab.wcf.like.likeableObject', 'com.woltlab.wcf.comment');

        $commentIDs = $responseIDs = [];
        foreach ($likes as $like) {
            if ($like->objectTypeID == $commentLikeObjectType->objectTypeID) {
                $commentIDs[] = $like->objectID;
            } else {
                $responseIDs[] = $like->objectID;
            }
        }

        // fetch response
        $userIDs = $responses = [];
        if (!empty($responseIDs)) {
            $responses = ViewableCommentResponseRuntimeCache::getInstance()->getObjects($responseIDs);

            foreach ($responses as $response) {
                $commentIDs[] = $response->commentID;
                if ($response->userID) {
                    $userIDs[] = $response->userID;
                }
            }
        }

        // fetch comments
        $comments = ViewableCommentRuntimeCache::getInstance()->getObjects($commentIDs);

        // fetch users
        $users = [];
        $eventIDs = [];
        foreach ($comments as $comment) {
            $eventIDs[] = $comment->objectID;
            if ($comment->userID) {
                $userIDs[] = $comment->userID;
            }
        }
        if (!empty($userIDs)) {
            $users = UserProfileRuntimeCache::getInstance()->getObjects(\array_unique($userIDs));
        }

        // fetch events
        $events = [];
        if (!empty($eventIDs)) {
            $eventList = new EventList();
            $eventList->setObjectIDs($eventIDs);
            $eventList->readObjects();
            $events = $eventList->getObjects();
        }

        // set message
        foreach ($likes as $like) {
            if ($like->objectTypeID == $commentLikeObjectType->objectTypeID) {
                // comment like
                if (isset($comments[$like->objectID])) {
                    $comment = $comments[$like->objectID];

                    if (
                        isset($events[$comment->objectID]) &&
                        $events[$comment->objectID]->canRead()
                    ) {
                        $like->setIsAccessible();

                        // short output
                        $text = WCF::getLanguage()->getDynamicVariable(
                            'wcf.like.title.de.md-raidplaner.rp.eventComment',
                            [
                                'commentAuthor' => $comment->userID ? $users[$comment->userID] : null,
                                'comment' => $comment,
                                'event' => $events[$comment->objectID],
                                'reaction' => $like,
                            ]
                        );
                        $like->setTitle($text);

                        // output
                        $like->setDescription($comment->getExcerpt());
                    }
                }
            } else {
                // response like
                if (isset($responses[$like->objectID])) {
                    $response = $responses[$like->objectID];
                    $comment = $comments[$response->commentID];

                    if (
                        isset($events[$comment->objectID]) &&
                        $events[$comment->objectID]->canRead()
                    ) {
                        $like->setIsAccessible();

                        // short output
                        $text = WCF::getLanguage()->getDynamicVariable(
                            'wcf.like.title.de.md-raidplaner.rp.eventComment.response',
                            [
                                'responseAuthor' => $comment->userID ? $users[$response->userID] : null,
                                'commentAuthor' => $comment->userID ? $users[$comment->userID] : null,
                                'event' => $events[$comment->objectID],
                                'reaction' => $like,
                                'response' => $response,
                            ]
                        );
                        $like->setTitle($text);

                        // output
                        $like->setDescription($response->getExcerpt());
                    }
                }
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function updateCounter($objectID, $value): void
    {
        $editor = new EventEditor(new Event($objectID));
        $editor->updateCounters([
            'comments' => $value,
        ]);
    }
}
