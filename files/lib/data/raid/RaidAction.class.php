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
    protected $permissionsCreate = ['mod.rp.canAddRaid'];
    protected $permissionsDelete = ['mod.rp.canDeleteRaid'];
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

    #[\Override]
    public function create(): Raid
    {
        $this->parameters['data']['game'] = \RP_CURRENT_GAME;
        $this->parameters['data']['addedBy'] = WCF::getUser()->username;

        $raid = parent::create();
        $raidEditor = new RaidEditor($raid);

        $attendeeIDs = $this->parameters['attendeeIDs'] ?? [];
        $raidEditor->addAttendees($attendeeIDs, false, $this->parameters['event']);

        $items = $this->parameters['items'] ?? [];
        $raidEditor->addItems($items, false);

        return $raid;
    }

    #[\Override]
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
