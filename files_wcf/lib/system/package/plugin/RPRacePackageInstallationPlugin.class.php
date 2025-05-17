<?php

namespace wcf\system\package\plugin;

use rp\data\game\GameCache;
use rp\data\race\Race;
use rp\data\race\RaceEditor;
use rp\data\race\RaceList;
use rp\system\cache\eager\data\GameCacheData;
use wcf\data\IStorableObject;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\devtools\pip\IDevtoolsPipEntryList;
use wcf\system\devtools\pip\IGuiPackageInstallationPlugin;
use wcf\system\devtools\pip\TXmlGuiPackageInstallationPlugin;
use wcf\system\exception\SystemException;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\field\ItemListFormField;
use wcf\system\form\builder\field\SingleSelectionFormField;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\TitleFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\form\builder\IFormDocument;
use wcf\system\language\LanguageFactory;
use wcf\system\Regex;
use wcf\system\WCF;

/**
 * Installs, updates and deletes races.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class RPRacePackageInstallationPlugin extends AbstractXMLPackageInstallationPlugin implements
    IGuiPackageInstallationPlugin,
    IUniqueNameXMLPackageInstallationPlugin
{
    use TXmlGuiPackageInstallationPlugin;

    /**
     * @inheritDoc
     */
    public $application = 'rp';
    public GameCacheData $cache;

    /**
     * @inheritDoc
     */
    public $className = RaceEditor::class;

    /**
     * list of factions per race id
     * @var string[]
     */
    public array $factions = [];

    /**
     * list of created or updated races by id
     * @var RaceEditor[]
     */
    protected array $races = [];

    /**
     * @inheritDoc
     */
    public $tableName = 'race';

    /**
     * @inheritDoc
     */
    public $tagName = 'race';

    /**
     * @inheritDoc
     */
    protected function addFormFields(IFormDocument $form): void
    {
        /** @var FormContainer $dataContainer */
        $dataContainer = $form->getNodeById('data');

        $dataContainer->appendChildren([
            TextFormField::create('identifier')
                ->label('wcf.acp.pip.rpRace.identifier')
                ->description('wcf.acp.pip.rpRace.identifier.description')
                ->required()
                ->addValidator(new FormFieldValidator('regex', function (TextFormField $formField) {
                    $regex = Regex::compile('^[A-z0-9\-\_]+$');

                    if (!$regex->match($formField->getSaveValue())) {
                        $formField->addValidationError(
                            new FormFieldValidationError(
                                'invalid',
                                'wcf.acp.pip.rpRace.identifier.error.invalid'
                            )
                        );
                    }
                }))
                ->addValidator(new FormFieldValidator('uniqueness', function (TextFormField $formField) {
                    if (
                        $formField->getDocument()->getFormMode() === IFormDocument::FORM_MODE_CREATE
                        || $this->editedEntry->getAttribute('identifier') !== $formField->getValue()
                    ) {
                        /** @var SingleSelectionFormField $gameFormField */
                        $gameFormField = $formField->getDocument()->getNodeById('game');
                        $gameID = $this->getGameCacheData()->getGameByIdentifier($gameFormField->getSaveValue())?->gameID;

                        $raceList = new RaceList();
                        $raceList->getConditionBuilder()->add('identifier = ?', [$formField->getValue()]);

                        if ($raceList->countObjects() > 0) {
                            $formField->addValidationError(
                                new FormFieldValidationError(
                                    'notUnique',
                                    'wcf.acp.pip.rpRace.identifier.error.noUnique'
                                )
                            );
                        }
                    }
                })),

            TitleFormField::create()
                ->required()
                ->i18n()
                ->i18nRequired()
                ->languageItemPattern('__NONE__'),

            SingleSelectionFormField::create('game')
                ->label('wcf.acp.pip.rpRace.game')
                ->description('wcf.acp.pip.rpRace.game.description')
                ->required()
                ->options(function () {
                    $options = [];
                    foreach ($this->getGameCacheData()->games as $game) {
                        $options[$game->identifier] = $game->getTitle();
                    }

                    \asort($options);

                    return $options;
                }),

            ItemListFormField::create('factions')
                ->label('wcf.acp.pip.rpRace.factions')
                ->description('wcf.acp.pip.rpRace.factions.description')
                ->saveValueType(ItemListFormField::SAVE_VALUE_TYPE_ARRAY)
                ->required(),

            TextFormField::create('icon')
                ->label('wcf.acp.pip.rpRace.icon')
                ->description('wcf.acp.pip.rpRace.icon.description'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function fetchElementData(\DOMElement $element, $saveData): array
    {
        $data = [
            'identifier' => $element->getAttribute('identifier'),
            'packageID' => $this->installation->getPackageID(),
            'title' => [],
        ];

        /** @var \DOMElement $title */
        foreach ($element->getElementsByTagName('title') as $title) {
            $data['title'][LanguageFactory::getInstance()->getLanguageByCode($title->getAttribute('language'))->languageID] = $title->nodeValue;
        }

        foreach (['game', 'icon'] as $optionalElementName) {
            $optionalElement = $element->getElementsByTagName($optionalElementName)->item(0);
            if ($optionalElement !== null) {
                $data[$optionalElementName] = $optionalElement->nodeValue;
            } elseif ($saveData) {
                $data[$optionalElementName] = '';
            }
        }

        $factions = $element->getElementsByTagName('factions')->item(0);
        if ($factions !== null) {
            $entry = [];
            /** @var \DOMElement $faction */
            foreach ($factions->getElementsByTagName('faction') as $faction) {
                $entry[] = $faction->nodeValue;
            }

            if (!empty($entry)) {
                $data['factions'] = $entry;
            }
        }

        if ($saveData) {
            if (isset($data['game'])) {
                $data['gameID'] = $this->getGameCacheData()->getGameByIdentifier($data['game'])?->gameID;
            }
            unset($data['game']);

            $titles = [];
            foreach ($data['title'] as $languageID => $title) {
                $titles[LanguageFactory::getInstance()->getLanguage($languageID)->languageCode] = $title;
            }
            $data['title'] = $titles;

            if (isset($data['factions'])) {
                $this->factions[$data['identifier']] = $data['factions'];
                unset($data['factions']);
            }
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    protected function findExistingItem(array $data): array
    {
        $sql = "SELECT  *
                FROM    rp" . WCF_N . "_race
                WHERE   identifier = ?
                    AND packageID = ?";
        $parameters = [
            $data['identifier'],
            $this->installation->getPackageID(),
        ];

        return [
            'sql' => $sql,
            'parameters' => $parameters,
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getDefaultFilename(): string
    {
        return 'rpRace.xml';
    }

    /**
     * @inheritDoc
     * @throws  SystemException
     */
    protected function getElement(\DOMXPath $xpath, array &$elements, \DOMElement $element): void
    {
        $nodeValue = $element->nodeValue;

        if ($element->tagName === 'title') {
            if (empty($element->getAttribute('language'))) {
                throw new SystemException("Missing required attribute 'language' for race item '" . $element->parentNode->getAttribute('identifier') . "'");
            }

            // <title> can occur multiple items using the `language` attribute
            $elements['title'] ??= [];

            $elements['title'][$element->getAttribute('language')] = $element->nodeValue;
        } else if ($element->tagName == 'factions') {
            $nodeValue = [];

            $factions = $xpath->query('child::ns:faction', $element);
            foreach ($factions as $faction) {
                $nodeValue[] = $faction->nodeValue;
            }

            $elements['factions'] = $nodeValue;
        } else {
            $elements[$element->tagName] = $nodeValue;
        }
    }

    /**
     * @inheritDoc
     */
    public function getElementIdentifier(\DOMElement $element): string
    {
        return $element->getAttribute('identifier');
    }

    public function getGameCacheData(): GameCacheData
    {
        if (!isset($this->cache)) {
            $this->cache = (new GameCache())->getCache();
        }

        return $this->cache;
    }

    /**
     * @inheritDoc
     */
    public function getNameByData(array $data): string
    {
        return $data['identifier'];
    }

    /**
     * @inheritDoc
     */
    public static function getSyncDependencies()
    {
        return ['language', 'rpGame', 'rpFaction'];
    }

    /**
     * @inheritDoc
     */
    protected function handleDelete(array $items)
    {
        $sql = "DELETE FROM rp1_race
                WHERE       identifier = ?
                    AND     packageID = ?";
        $statement = WCF::getDB()->prepare($sql);

        $sql = "DELETE FROM wcf1_language_item
                WHERE       languageItem = ?";
        $languageItemStatement = WCF::getDB()->prepare($sql);

        WCF::getDB()->beginTransaction();
        foreach ($items as $item) {
            $statement->execute([
                $item['attributes']['identifier'],
                $this->installation->getPackageID(),
            ]);

            $languageItemStatement->execute([
                'rp.race.' . $item['attributes']['identifier'],
            ]);
        }
        WCF::getDB()->commitTransaction();
    }

    /**
     * @inheritDoc
     */
    protected function import(array $row, array $data): IStorableObject
    {
        $race = parent::import($row, $data);

        $this->races[$race->raceID] = ($race instanceof Race) ? new RaceEditor($race) : $race;

        return $race;
    }

    /**
     * @inheritDoc
     */
    protected function postImport(): void
    {
        if (empty($this->factions)) {
            return;
        }

        $conditions = new PreparedStatementConditionBuilder();
        $conditions->add('identifier IN (?)', [\array_keys($this->factions)]);
        $conditions->add('packageID = ?', [$this->installation->getPackageID()]);

        $sql = "SELECT  *
                FROM    rp1_race
                {$conditions}";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute($conditions->getParameters());

        /** @var Race[] $races */
        $races = $statement->fetchObjects(Race::class, 'identifier');

        // save factions
        $sql = "DELETE FROM rp1_race_to_faction
                WHERE       raceID = ?";
        $deleteStatement = WCF::getDB()->prepare($sql);

        $sql = "INSERT IGNORE   rp1_race_to_faction
                                (raceID, factionID)
                VALUES          (?, ?)";
        $insertStatement = WCF::getDB()->prepare($sql);

        foreach ($this->factions as $raceIdentifier => $factions) {
            // delete old factions
            $deleteStatement->execute([$races[$raceIdentifier]->raceID]);

            // get faction ids
            $conditionBuilder = new PreparedStatementConditionBuilder();
            $conditionBuilder->add('identifier IN (?)', [$factions]);
            $sql = "SELECT  factionID
                    FROM    rp1_faction
                    {$conditionBuilder}";
            $statement = WCF::getDB()->prepare($sql);
            $statement->execute($conditionBuilder->getParameters());
            $factionIDs = $statement->fetchAll(\PDO::FETCH_COLUMN);

            // save faction ids
            foreach ($factionIDs as $factionID) {
                $insertStatement->execute([
                    $races[$raceIdentifier]->raceID,
                    $factionID,
                ]);
            }
        }
    }

    /**
     * @inheritDoc
     */
    protected function prepareXmlElement(\DOMDocument $document, IFormDocument $form): \DOMElement
    {
        $formData = $form->getData();
        $data = $formData['data'];

        $race = $document->createElement($this->tagName);
        $race->setAttribute('identifier', $data['identifier']);

        if (!empty($data['game'])) {
            $race->appendChild($document->createElement('game', $data['game']));
        }

        foreach ($formData['title_i18n'] as $languageID => $title) {
            $title = $document->createElement('title', $this->getAutoCdataValue($title));
            $title->setAttribute('language', LanguageFactory::getInstance()->getLanguage($languageID)->languageCode);

            $race->appendChild($title);
        }

        $this->appendElementChildren(
            $race,
            [
                'icon' => '',
            ],
            $form
        );

        if (!empty($formData['factions'])) {
            $factions = $document->createElement('factions');

            \sort($formData['factions']);
            foreach ($formData['factions'] as $faction) {
                $factions->appendChild($document->createElement('faction', $faction));
            }

            $race->appendChild($factions);
        }

        return $race;
    }

    /**
     * @inheritDoc
     * @throws  SystemException
     */
    protected function prepareImport(array $data): array
    {
        $gameID = $this->getGameCacheData()->getGameByIdentifier($data['elements']['game'] ?? '')?->gameID;

        if ($gameID === null) {
            throw new SystemException("The race '" . $data['attributes']['identifier'] . "' must either have an associated game or unable to find game '" . $data['elements']['game'] . "'.");
        }

        if (!empty($data['elements']['factions'])) {
            $this->factions[$data['attributes']['identifier']] = $data['elements']['factions'];
        }

        return [
            'gameID' => $gameID,
            'icon' => $data['elements']['icon'] ?? '',
            'identifier' => $data['attributes']['identifier'],
            'title' => $this->getI18nValues($data['elements']['title']),
        ];
    }

    /**
     * @inheritDoc
     */
    protected function setEntryListKeys(IDevtoolsPipEntryList $entryList): void
    {
        $entryList->setKeys([
            'identifier' => 'wcf.acp.pip.rpRace.identifier',
            'game' => 'wcf.acp.pip.rpRace.game',
        ]);
    }
}
