<?php

namespace rp\data\raid;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\WCF;

/**
 * Executes raid related actions.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @method  RaidEditor[]    getObjects()
 * @method  RaidEditor  getSingleObject()
 */
class RaidAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $permissionsCreate = ['mod.rp.canAddRaid'];

    /**
     * @inheritDoc
     */
    protected $permissionsDelete = ['mod.rp.canDeleteRaid'];

    /**
     * @inheritDoc
     */
    protected $className = RaidEditor::class;

    /**
     * Add attendees to given raid.
     */
    public function addAttendees(): void
    {
        if (empty($this->objects)) {
            $this->readObjects();
        }

        $attendeeIDs = $this->parameters['attendeeIDs'] ?? [];
        $deleteOldAttendees = $this->parameters['deleteOldAttendees'] ?? true;

        foreach ($this->getObjects() as $raid) {
            $raid->addAttendees($attendeeIDs, $deleteOldAttendees);
        }

        RaidEditor::resetCache();
    }

    /**
     * Add items to given raid.
     */
    public function addItems(): void
    {
        if (empty($this->objects)) {
            $this->readObjects();
        }

        $itemIDs = $this->parameters['itemIDs'] ?? [];
        $deleteOldItems = $this->parameters['deleteOldItems'] ?? true;

        foreach ($this->getObjects() as $raid) {
            $raid->addItems($itemIDs, $deleteOldItems);
        }

        RaidEditor::resetCache();
    }

    /**
     * @inheritDoc
     */
    public function create(): Raid
    {
        $this->parameters['data']['gameID'] = RP_CURRENT_GAME_ID;
        $this->parameters['data']['addedBy'] = WCF::getUser()->username;

        $raid = parent::create();
        $raidEditor = new RaidEditor($raid);

        $attendeeIDs = $this->parameters['attendeeIDs'] ?? [];
        $raidEditor->addAttendees($attendeeIDs, false, $this->parameters['event']);

        $items = $this->parameters['items'] ?? [];
        $raidEditor->addItems($items, false);

        return $raid;
    }

    /**
     * @inheritDoc
     */
    public function update(): void
    {
        $this->parameters['data']['updatedBy'] = WCF::getUser()->username;

        parent::update();

        $attendeeIDs = $this->parameters['attendeeIDs'] ?? [];
        if (!empty($attendeeIDs)) {
            $action = new self($this->objects, 'addAttendees', [
                'attendeeIDs' => $attendeeIDs,
            ]);
            $action->executeAction();
        }

        $items = $this->parameters['items'] ?? [];
        if (!empty($items)) {
            $action = new self($this->objects, 'addItems', [
                'items' => $items,
            ]);
            $action->executeAction();
        }
    }
}
