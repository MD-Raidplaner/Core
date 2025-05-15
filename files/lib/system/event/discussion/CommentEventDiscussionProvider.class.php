<?php

namespace rp\system\event\discussion;

use rp\data\event\Event;
use wcf\system\comment\CommentHandler;
use wcf\system\WCF;

/**
 * The built-in discussion provider using the native comment system.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class CommentEventDiscussionProvider extends AbstractEventDiscussionProvider
{
    /**
     * @inheritDoc
     */
    public function getDiscussionCount(): int
    {
        return $this->event->comments;
    }

    /**
     * @inheritDoc
     */
    public function getDiscussionCountPhrase(): string
    {
        return WCF::getLanguage()->getDynamicVariable('rp.event.eventComments', [
            'event' => $this->event,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getDiscussionLink(): string
    {
        return $this->event->getLink() . '#comments';
    }

    /**
     * @inheritDoc
     */
    public static function isResponsible(Event $event): bool
    {
        return !!$event->enableComments;
    }

    /**
     * @inheritDoc
     */
    public function renderDiscussions(): string
    {
        $commentCanAdd = WCF::getSession()->getPermission('user.rp.canAddEventComment');
        $commentObjectTypeID = CommentHandler::getInstance()->getObjectTypeID('de.md-raidplaner.rp.eventComment');
        $commentManager = CommentHandler::getInstance()->getObjectType($commentObjectTypeID)->getProcessor();
        $commentList = CommentHandler::getInstance()->getCommentList(
            $commentManager,
            $commentObjectTypeID,
            $this->event->eventID
        );

        WCF::getTPL()->assign([
            'commentCanAdd' => $commentCanAdd,
            'commentList' => $commentList,
            'commentObjectTypeID' => $commentObjectTypeID,
            'lastCommentTime' => $commentList->getMinCommentTime(),
            'likeData' => (MODULE_LIKE) ? $commentList->getLikeData() : [],
        ]);

        return WCF::getTPL()->fetch('eventComments', 'rp');
    }
}
