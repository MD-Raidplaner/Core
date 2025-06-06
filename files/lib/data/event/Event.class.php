<?php

namespace rp\data\event;

use rp\system\event\discussion\CommentEventDiscussionProvider;
use rp\system\event\discussion\IEventDiscussionProvider;
use rp\system\event\discussion\VoidEventDiscussionProvider;
use rp\system\event\IEventController;
use rp\system\event\RaidEventController;
use wcf\data\DatabaseObject;
use wcf\data\IMessage;
use wcf\data\ITitledLinkObject;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\TUserContent;
use wcf\data\user\UserProfile;
use wcf\system\html\output\HtmlOutputProcessor;
use wcf\system\request\IRouteController;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\DateUtil;
use wcf\util\MessageUtil;
use wcf\util\StringUtil;

/**
 * Represents a event.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @property-read   int $eventID        unique id of the event
 * @property-read   int $objectTypeID       id of the event controller object type
 * @property-read   string|null $title      name of the event
 * @property-read   int|null    $userID id of the user who created the event or `null` if the user does not exist anymore
 * @property-read   string  $username       name of the user who created the event
 * @property-read   int $created        timestamp at which the event has been created
 * @property-read   int $startTime      timestamp for start the event
 * @property-read   int $endTime        timestamp for end the event
 * @property-read   int $isFullDay      is `1` if the event occurs all day long, otherwise `0`
 * @property-read   string  $notes      notes of the event
 * @property-read   int $hasEmbeddedObjects     is `1` if there are embedded objects in the event, otherwise `0`
 * @property-read   int $views      number of times the event has been viewed
 * @property-read   int $enableComments     is `1` if comments are enabled for the event, otherwise `0`
 * @property-read   int $comments       number of comments on the event
 * @property-read   int $cumulativeLikes        cumulative result of likes (counting `+1`) and dislikes (counting `-1`) for the event
 * @property-read   array   $additionalData     array with additional data of the event
 * @property-read	int $deleteTime     timestamp at which the event has been deleted
 * @property-read	int $isDeleted      is `1` if the event is in trash bin, otherwise `0`
 * @property-read   int $isCanceled     is `1` if the even is canceled, otherwise `0`
 * @property-read   int $isDisabled     is `1` if the even is disabled, otherwise `0`
 */
final class Event extends DatabaseObject implements ITitledLinkObject, IRouteController, IMessage
{
    use TUserContent;

    protected ?IEventController $controller = null;
    protected ?IEventDiscussionProvider $discussionProvider = null;
    private \DateTime $endTimeObj;
    private \DateTime $startTimeObj;

    public function __construct($id, ?array $row = null, ?self $object = null)
    {
        parent::__construct($id, $row, $object);

        $this->startTimeObj = new \DateTime('@' . $this->startTime);
        $this->startTimeObj->setTimezone(WCF::getUser()->getTimeZone());

        $this->endTimeObj = new \DateTime('@' . $this->endTime);
        $this->endTimeObj->setTimezone(WCF::getUser()->getTimeZone());
    }

    /**
     * Returns true if the current user can cancel this event.
     */
    public function canCancel(): bool
    {
        if (!$this->isRaidEvent()) {
            return false;
        }

        if ($this->canEdit()) {
            return true;
        }

        return false;
    }

    /**
     * Returns true if the current user can delete these event.
     */
    public function canDelete(): bool
    {
        // check mod permissions
        if (WCF::getSession()->getPermission('mod.rp.canDeleteEventCompletely')) {
            return true;
        }

        return false;
    }

    /**
     * Returns true if the current user can edit these event.
     */
    public function canEdit(): bool
    {
        // check mod permissions
        if (WCF::getSession()->getPermission('mod.rp.canEditEvent')) {
            return true;
        }

        if ($this->isRaidEvent()) {
            if ($this->getController()->isLeader()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns true if this user can edit his event.
     */
    public function canEditOwnEvent(): bool
    {
        if (
            $this->userID &&
            $this->userID == WCF::getUser()->userID &&
            WCF::getSession()->getPermission('user.rp.canEditOwnEvent')
        ) {
            return true;
        }

        return false;
    }

    /**
     * Returns true if the given user has access to this event. If the given $user is null,
     * the function uses the current user.
     */
    public function canRead(?UserProfile $user = null): bool
    {
        if ($user === null) {
            $user = new UserProfile(WCF::getUser());
        }

        if ($this->isDeleted) {
            if (!$user->getPermission('mod.rp.canViewDeletedEvent')) {
                return false;
            }
        }

        if ($this->isDisabled) {
            if (!$user->getPermission('mod.rp.canModerateEvent')) {
                return false;
            }
        }

        if (!$user->getPermission('user.rp.canReadEvent')) {
            return false;
        }

        return true;
    }

    /**
     * Returns true if the current user can restore this event.
     */
    public function canRestore(): bool
    {
        if (WCF::getSession()->getPermission('mod.rp.canRestoreEvent')) {
            return true;
        }

        return false;
    }

    /**
     * Returns true if the current user can trash this event.
     */
    public function canTrash(): bool
    {
        if (WCF::getSession()->getPermission('mod.rp.canDeleteEvent')) {
            return true;
        }

        // check user permissions
        if (
            $this->userID &&
            $this->userID == WCF::getUser()->userID &&
            WCF::getSession()->getPermission('user.rp.canDeleteOwnEvent')
        ) {
            return true;
        }

        return false;
    }

    /**
     * Returns the list of the available discussion providers.
     *
     * @return      string[]
     */
    public static function getAllDiscussionProviders(): array
    {
        /** @var string[] $discussionProviders */
        static $discussionProviders;

        if ($discussionProviders === null) {
            $discussionProviders = [];

            $objectTypes = ObjectTypeCache::getInstance()->getObjectTypes('de.md-raidplaner.rp.event.discussionProvider');
            $commentProvider = '';
            foreach ($objectTypes as $objectType) {
                // the comment and the "void" provider should always be the last in the list
                if ($objectType->className === CommentEventDiscussionProvider::class) {
                    $commentProvider = $objectType->className;
                    continue;
                }

                $discussionProviders[] = $objectType->className;
            }

            $discussionProviders[] = $commentProvider;
            $discussionProviders[] = VoidEventDiscussionProvider::class;
        }

        return $discussionProviders;
    }

    /**
     * Returns the event controller.
     */
    public function getController(): IEventController
    {
        if ($this->controller === null) {
            $className = ObjectTypeCache::getInstance()->getObjectType($this->objectTypeID)->className;

            $this->controller = new $className();
            $this->controller->setEvent($this);
        }

        return $this->controller;
    }

    /**
     * Returns the responsible discussion provider for this event.
     */
    public function getDiscussionProvider(): IEventDiscussionProvider
    {
        if ($this->discussionProvider === null) {
            foreach (self::getAllDiscussionProviders() as $discussionProvider) {
                if (\call_user_func([$discussionProvider, 'isResponsible'], $this)) {
                    $this->setDiscussionProvider(new $discussionProvider($this));
                    break;
                }
            }

            if ($this->discussionProvider === null) {
                throw new \RuntimeException('No discussion provider has claimed to be responsible for the event #' . $this->eventID);
            }
        }

        return $this->discussionProvider;
    }

    #[\Override]
    public function getExcerpt($maxLength = 255): string
    {
        return  MessageUtil::truncateFormattedMessage($this->getSimplifiedFormattedNotes(), $maxLength);
    }

    /**
     * Returns the formatted end time of the event.
     */
    public function getFormattedEndTime(bool $short = false): string
    {
        $language = WCF::getLanguage();
        $format = $language->get(DateUtil::TIME_FORMAT);

        if (!$short && !$this->isSelfDay()) {
            $format = $language->get(DateUtil::DATE_FORMAT) . ' ' . $format;
        }

        return DateUtil::format($this->endTimeObj, $format);
    }

    #[\Override]
    public function getFormattedMessage(): string
    {
        $processor = new HtmlOutputProcessor();
        $processor->enableUgc = false;
        $processor->process($this->notes, 'de.md-raidplaner.rp.event.notes', $this->eventID, false);

        return $processor->getHtml();
    }

    /**
     * Returns the formatted start time of the event.
     */
    public function getFormattedStartTime(bool $short = false): string
    {
        $language = WCF::getLanguage();
        $format = $language->get(DateUtil::TIME_FORMAT);

        if (!$short && !$this->isSelfDay()) {
            $format = $language->get(DateUtil::DATE_FORMAT) . ' ' . $format;
        }

        return DateUtil::format($this->startTimeObj, $format);
    }

    /**
     * Returns the html code to display the icon.
     */
    public function getIcon(int $size = 16): string
    {
        return $this->getController()->getIcon($size);
    }

    #[\Override]
    public function getLink(): string
    {
        return LinkHandler::getInstance()->getLink('Event', [
            'application' => 'rp',
            'object' => $this,
            'forceFrontend' => true
        ]);
    }

    #[\Override]
    public function getMessage(): string
    {
        return $this->notes;
    }

    /**
     * Returns a plain version of the formatted notes.
     */
    public function getPlainFormattedNotes(): string
    {
        $processor = new HtmlOutputProcessor();
        $processor->setOutputType('text/plain');
        $processor->enableUgc = false;
        $processor->process($this->notes, 'de.md-raidplaner.rp.event.notes', $this->eventID, false);

        return StringUtil::encodeHTML(StringUtil::truncate($processor->getHtml(), 255));
    }

    /**
     * Returns a simplified version of the formatted notes.
     */
    public function getSimplifiedFormattedNotes(): string
    {
        $processor = new HtmlOutputProcessor();
        $processor->setOutputType('text/simplified-html');
        $processor->process($this->notes, 'de.md-raidplaner.rp.event.notes', $this->eventID);

        return $processor->getHtml();
    }

    #[\Override]
    public function getTime(): int
    {
        return $this->created;
    }

    #[\Override]
    public function getTitle(): string
    {
        return $this->getController()->getTitle();
    }

    #[\Override]
    protected function handleData($data): void
    {
        parent::handleData($data);

        // unserialize additional data
        $this->data['additionalData'] = (empty($data['additionalData']) ? [] : @\unserialize($data['additionalData']));
    }

    protected function isSelfDay(): bool
    {
        return $this->startTimeObj->format('Y-m-d') === $this->endTimeObj->format('Y-m-d');
    }

    /**
     * Returns `true` if this event is a raid event, otherwise `false`.
     */
    public function isRaidEvent(): bool
    {
        if ($this->getController() instanceof RaidEventController) return true;
        return false;
    }

    #[\Override]
    public function isVisible(): bool
    {
        return $this->canRead();
    }

    /**
     * Sets the discussion provider for this event.
     */
    public function setDiscussionProvider(IEventDiscussionProvider $discussionProvider): void
    {
        $this->discussionProvider = $discussionProvider;
    }

    #[\Override]
    public function __get($name): mixed
    {
        $value = parent::__get($name);

        // treat additional data as data variables if it is an array
        $value ??= $this->data['additionalData'][$name] ?? null;

        return $value;
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->getFormattedMessage();
    }
}
