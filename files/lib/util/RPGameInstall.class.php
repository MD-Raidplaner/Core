<?php

namespace rp\util;

use rp\data\point\account\PointAccount;
use rp\data\point\account\PointAccountEditor;
use rp\data\raid\event\RaidEvent;
use rp\data\raid\event\RaidEventEditor;
use wcf\data\language\item\LanguageItemAction;
use wcf\system\language\LanguageFactory;
use wcf\util\StringUtil;

/**
 * Game install helper
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class RPGameInstall
{
    public function __construct(
        private readonly int $gameID,
        private readonly string $pointAccountName,
        private readonly array $events,
        private readonly int $packageID,
    ) {}

    /**
     * Creates a point account.
     */
    private function createPointAccount(): PointAccount
    {
        return PointAccountEditor::create([
            'gameID' => $this->gameID,
            'title' => $this->pointAccountName,
        ]);
    }

    /**
     * Inserts events
     */
    private function insertEvent(PointAccount $pointAccount): void
    {
        foreach ($this->events as $event) {
            $eventObj = RaidEventEditor::create([
                'gameID' => $this->gameID,
                'pointAccountID' => $pointAccount->getObjectID(),
                'icon' => $event['icon'],
            ]);

            $eventEditor = new RaidEventEditor($eventObj);
            $eventEditor->update([
                'title' => \sprintf(
                    'rp.raid.event.title%d',
                    $eventObj->getObjectID()
                )
            ]);

            $this->insertLanguageItem($eventObj, $event['title']);
        }
    }

    /**
     * Inserts language items for the event.
     */
    private function insertLanguageItem(RaidEvent $event, array $eventTitle): void
    {
        foreach (LanguageFactory::getInstance()->getLanguages() as $language) {
            $languageCode = $language->languageCode;
            if (!isset($eventTitle[$languageCode])) {
                continue;
            }

            (new LanguageItemAction([], 'create', [
                'data' => [
                    'languageID' => $language->getObjectID(),
                    'languageItem' => \sprintf(
                        'rp.raid.event.title%d',
                        $event->getObjectID()
                    ),
                    'languageItemValue' => StringUtil::trim($eventTitle[$languageCode]),
                    'languageCategoryID' => (LanguageFactory::getInstance()->getCategory('rp.raid.event'))->languageCategoryID,
                    'packageID' => $this->packageID,
                    'languageItemOriginIsSystem' => 1,
                ],
            ]))->executeAction();
        }
    }

    /**
     * Installs the game configuration.
     */
    public function install(): void
    {
        $pointAccount = $this->createPointAccount();
        $this->insertEvent($pointAccount);
    }
}
