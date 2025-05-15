<?php

namespace rp\form;

use CuyZ\Valinor\Mapper\MappingError;
use rp\data\event\Event;
use rp\data\event\EventAction;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\object\type\ObjectType;
use wcf\data\object\type\ObjectTypeCache;
use wcf\form\AbstractForm;
use wcf\form\AbstractFormBuilderForm;
use wcf\http\Helper;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\request\IRouteController;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\HeaderUtil;

/**
 * Shows the event add form.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
class EventAddForm extends AbstractFormBuilderForm
{
    /**
     * @inheritDoc
     */
    public $objectActionClass = EventAction::class;

    /**
     * event controller
     */
    public ?ObjectType $eventController = null;

    /**
     * @inheritDoc
     */
    protected function createForm(): void
    {
        parent::createForm();

        $this->eventController->getProcessor()->createForm($this->form);
    }

    /**
     * @inheritDoc
     */
    public function readParameters(): void
    {
        parent::readParameters();

        try {
            $parameters = Helper::mapQueryParameters(
                $_GET,
                <<<'EOT'
                    array {
                        type?: string
                    }
                    EOT
            );

            $this->eventController = ObjectTypeCache::getInstance()->getObjectTypeByName('de.md-raidplaner.rp.event.controller', $parameters['type'] ?? '');
            if ($this->eventController === null) {
                throw new IllegalLinkException();
            }

            if (!$this->eventController->getProcessor()->isAccessible()) {
                throw new PermissionDeniedException();
            }
        } catch (MappingError) {
            throw new IllegalLinkException();
        }
    }

    public function save(): void
    {
        AbstractForm::save();

        $action = $this->formAction;
        if ($this->objectActionName) {
            $action = $this->objectActionName;
        } elseif ($this->formAction === 'edit') {
            $action = 'update';
        }

        $formData = $this->form->getData();
        $formData['data'] ??= [];
        $formData['data'] = \array_merge($this->additionalFields, $formData['data']);

        /** @var AbstractDatabaseObjectAction objectAction */
        $this->objectAction = new $this->objectActionClass(
            \array_filter([$this->formObject]),
            $action,
            $this->eventController->getProcessor()->saveForm($formData)
        );
        $event = $this->objectAction->executeAction()['returnValues'];

        if ($event->isDisabled) {
            HeaderUtil::delayedRedirect(LinkHandler::getInstance()->getLink('Calendar', [
                'application' => 'rp'
            ]), WCF::getLanguage()->getDynamicVariable('rp.event.moderation.redirect'), 30);
        } else {
            HeaderUtil::redirect($event->getLink());
        }
        exit;
    }

    /**
     * @inheritDoc
     */
    public function saved(): void
    {
        AbstractForm::saved();

        /** @var Event $event */
        $event = $this->objectAction->getReturnValues()['returnValues'];

        if ($event->isDisabled) {
            HeaderUtil::delayedRedirect(LinkHandler::getInstance()->getLink('Calendar', [
                'application' => 'rp'
            ]), WCF::getLanguage()->getDynamicVariable('rp.event.moderation.redirect'), 30);
        } else {
            HeaderUtil::redirect($event->getLink());
        }
        exit;
    }

    /**
     * @inheritDoc
     */
    protected function setFormAction(): void
    {
        $parameters = [
            'type' => $this->eventController->objectType,
        ];
        if ($this->formObject !== null) {
            if ($this->formObject instanceof IRouteController) {
                $parameters['object'] = $this->formObject;
            } else {
                $object = $this->formObject;

                $parameters['id'] = $object->{$object::getDatabaseTableIndexName()};
            }
        }

        $this->form->action(LinkHandler::getInstance()->getControllerLink(static::class, $parameters));
    }
}
