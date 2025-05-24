<?php

namespace rp\data\raid\event;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\package\PackageCache;
use wcf\system\file\upload\UploadFile;
use wcf\system\language\I18nHandler;

/**
 * Executes race related actions.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @method  RaidEventEditor[]   getObjects()
 * @method  RaidEventEditor     getSingleObject()
 */
class RaidEventAction extends AbstractDatabaseObjectAction
{
    protected $className = RaidEventEditor::class;
    protected $permissionsCreate = ['admin.rp.canManageRaidEvent'];
    protected $permissionsDelete = ['admin.rp.canManageRaidEvent'];
    protected $permissionsUpdate = ['admin.rp.canManageRaidEvent'];
    protected $requireACP = ['create', 'delete', 'update'];

    #[\Override]
    public function create(): RaidEvent
    {
        $this->parameters['data']['gameID'] ??= RP_CURRENT_GAME_ID;

        // The title cannot be empty by design, but cannot be filled proper if the
        // multilingualism is enabled, therefore, we must fill the title with a dummy value.
        if (!isset($this->parameters['data']['title']) && isset($this->parameters['title_i18n'])) {
            $this->parameters['data']['title'] = 'wcf.global.name';
        }

        /** @var RaidEvent $event */
        $event = parent::create();

        $updateData = [];

        // i18n
        if (isset($this->parameters['title_i18n'])) {
            I18nHandler::getInstance()->save(
                $this->parameters['title_i18n'],
                'rp.raid.event.title' . $event->eventID,
                'rp.raid.event',
                PackageCache::getInstance()->getPackageID('de.md-raidplaner.rp')
            );

            $updateData['title'] = 'rp.raid.event.title' . $event->eventID;
        }

        // image
        if (empty($event->icon) && isset($this->parameters['iconFile']) && \is_array($this->parameters['iconFile'])) {
            $iconFile = \reset($this->parameters['iconFile']);
            if (!($iconFile instanceof UploadFile)) {
                throw new \InvalidArgumentException("The parameter 'icon' is no instance of '" . UploadFile::class . "', instance of '" . \get_class($iconFile) . "' given.");
            }

            // save new image
            if (!$iconFile->isProcessed()) {
                $fileName = $iconFile->getFilename();

                \rename($iconFile->getLocation(), RP_DIR . '/images/raid/event/' . $fileName);
                $iconFile->setProcessed(RP_DIR . '/images/raid/event/' . $fileName);

                $ext = \explode('.', $filename);
                \array_pop($ext);
                $updateData['icon'] = \implode($ext);
            }
        }

        if (!empty($updateData)) {
            $eventEditor = new RaidEventEditor($event);
            $eventEditor->update($updateData);
            $event = new RaidEvent($event->eventID);
        }

        return $event;
    }

    #[\Override]
    public function update(): void
    {
        parent::update();

        foreach ($this->getObjects() as $object) {
            $updateData = [];

            // i18n
            if (isset($this->parameters['title_i18n'])) {
                I18nHandler::getInstance()->save(
                    $this->parameters['title_i18n'],
                    'rp.raid.event.title' . $object->eventID,
                    'rp.raid.event',
                    PackageCache::getInstance()->getPackageID('de.md-raidplaner.rp')
                );

                $updateData['title'] = 'rp.raid.event.title' . $object->eventID;
            }

            if (!empty($updateData)) {
                $object->update($updateData);
            }
        }
    }
}
