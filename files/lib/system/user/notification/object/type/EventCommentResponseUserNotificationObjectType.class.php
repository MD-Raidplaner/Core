<?php

namespace rp\system\user\notification\object\type;

use wcf\data\comment\response\CommentResponse;
use wcf\data\comment\response\CommentResponseList;
use wcf\system\user\notification\object\CommentResponseUserNotificationObject;
use wcf\system\user\notification\object\type\AbstractUserNotificationObjectType;

/**
 * Represents a comment notification object type for comment responses on events.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class EventCommentResponseUserNotificationObjectType extends AbstractUserNotificationObjectType
{
    /**
     * @inheritDoc
     */
    protected static $decoratorClassName = CommentResponseUserNotificationObject::class;

    /**
     * @inheritDoc
     */
    protected static $objectClassName = CommentResponse::class;

    /**
     * @inheritDoc
     */
    protected static $objectListClassName = CommentResponseList::class;
}
