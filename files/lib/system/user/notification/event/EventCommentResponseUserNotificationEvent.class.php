<?php

namespace rp\system\user\notification\event;

use rp\system\cache\runtime\ViewableEventRuntimeCache;
use wcf\system\email\Email;
use wcf\system\user\notification\event\AbstractCommentResponseUserNotificationEvent;
use wcf\system\user\notification\event\ITestableUserNotificationEvent;
use wcf\system\user\notification\event\TTestableCommentResponseUserNotificationEvent;

/**
 * User notification event for event comment responses.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class EventCommentResponseUserNotificationEvent  extends AbstractCommentResponseUserNotificationEvent implements
    ITestableUserNotificationEvent
{
    use TTestableCommentResponseUserNotificationEvent;
    use TTestableEventCommentUserNotificationEvent;

    #[\Override]
    public function getEmailMessage($notificationType = 'instant'): array
    {
        $messageID = \sprintf(
            '<de.md-raidplaner.rp.eventComment.notification/%d@%s>',
            $this->getUserNotificationObject()->commentID,
            Email::getHost()
        );

        return [
            'template' => 'email_notification_commentResponse',
            'in-reply-to' => [$messageID],
            'references' => [$messageID],
            'application' => 'wcf',
            'variables' => [
                'commentID' => $this->getUserNotificationObject()->commentID,
                'eventObj' => ViewableEventRuntimeCache::getInstance()
                    ->getObject($this->additionalData['objectID']),
                'languageVariablePrefix' => 'rp.user.notification.eventComment.response',
                'responseID' => $this->getUserNotificationObject()->responseID,
            ],
        ];
    }

    #[\Override]
    public function getLink(): string
    {
        return ViewableEventRuntimeCache::getInstance()->getObject($this->additionalData['objectID'])->getLink() . '#comment' . $this->getUserNotificationObject()->commentID . '/response' . $this->getUserNotificationObject()->responseID;
    }

    #[\Override]
    public function getMessage(): string
    {
        $authors = $this->getAuthors();
        if (\count($authors) > 1) {
            if (isset($authors[0])) {
                unset($authors[0]);
            }
            $count = \count($authors);

            return $this->getLanguage()->getDynamicVariable(
                'rp.user.notification.eventComment.response.message.stacked',
                [
                    'author' => $this->author,
                    'authors' => \array_values($authors),
                    'commentID' => $this->getUserNotificationObject()->commentID,
                    'count' => $count,
                    'event' => ViewableEventRuntimeCache::getInstance()
                        ->getObject($this->additionalData['objectID']),
                    'guestTimesTriggered' => $this->notification->guestTimesTriggered,
                    'others' => $count - 1,
                    'responseID' => $this->getUserNotificationObject()->responseID,
                ]
            );
        }

        return $this->getLanguage()->getDynamicVariable('rp.user.notification.eventComment.response.message', [
            'author' => $this->author,
            'commentID' => $this->getUserNotificationObject()->commentID,
            'event' => ViewableEventRuntimeCache::getInstance()
                ->getObject($this->additionalData['objectID']),
            'responseID' => $this->getUserNotificationObject()->responseID,
        ]);
    }

    #[\Override]
    protected function getObjectTitle(): string
    {
        return ViewableEventRuntimeCache::getInstance()
            ->getObject($this->additionalData['objectID'])->getTitle();
    }

    #[\Override]
    protected function getTypeName(): string
    {
        return $this->getLanguage()->get('wcf.user.recentActivity.de.md-raidplaner.rp.event.recentActivityEvent');
    }

    #[\Override]
    protected function prepare(): void
    {
        parent::prepare();

        ViewableEventRuntimeCache::getInstance()->cacheObjectID($this->additionalData['objectID']);
    }
}
