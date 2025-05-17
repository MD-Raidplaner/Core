<?php

namespace wcf\system\package\plugin;

use rp\data\classification\Classification;
use rp\data\classification\ClassificationEditor;
use rp\data\classification\ClassificationList;
use rp\system\cache\eager\data\GameCacheData;
use rp\system\cache\eager\GameCache;
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
 * Installs, updates and deletes classifications.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class RPClassificationPackageInstallationPlugin extends AbstractXMLPackageInstallationPlugin implements
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
     * list of created or updated classifications by id
     * @var ClassificationEditor[]
     */
    protected array $classifications = [];

    /**
     * @inheritDoc
     */
    public $className = ClassificationEditor::class;

    /**
     * list of factions per classification id
     * @var string[]
     */
    public array $factions = [];

    /**
     * list of races per classification id
     * @var string[]
     */
    public array $races = [];

    /**
     * list of roles per classification id
     * @var string[]
     */
    public array $roles = [];

    /**
     * list of skills per classification id
     * @var string[]
     */
    public array $skills = [];

    /**
     * @inheritDoc
     */
    public $tableName = 'classification';

    /**
     * @inheritDoc
     */
    public $tagName = 'classification';

    /**
     * @inheritDoc
     */
    protected function addFormFields(IFormDocument $form): void
    {
        /** @var FormContainer $dataContainer */
        $dataContainer = $form->getNodeById('data');

        $dataContainer->appendChildren([
            TextFormField::create('identifier')
                ->label('wcf.acp.pip.rpClassification.identifier')
                ->description('wcf.acp.pip.rpClassification.identifier.description')
                ->required()
                ->addValidator(new FormFieldValidator('regex', function (TextFormField $formField) {
                    $regex = Regex::compile('^[A-z0-9\-\_]+$');

                    if (!$regex->match($formField->getSaveValue())) {
                        $formField->addValidationError(
                            new FormFieldValidationError(
                                'invalid',
                                'wcf.acp.pip.rpClassification.identifier.error.invalid'
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

                        $classificationList = new ClassificationList();
                        $classificationList->getConditionBuilder()->add('identifier = ?', [$formField->getValue()]);

                        if ($classificationList->countObjects() > 0) {
                            $formField->addValidationError(
                                new FormFieldValidationError(
                                    'notUnique',
                                    'wcf.acp.pip.rpClassification.identifier.error.noUnique'
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
                ->label('wcf.acp.pip.rpClassification.game')
                ->description('wcf.acp.pip.rpClassification.game.description')
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
                ->label('wcf.acp.pip.rpClassification.factions')
                ->description('wcf.acp.pip.rpClassification.factions.description')
                ->saveValueType(ItemListFormField::SAVE_VALUE_TYPE_ARRAY)
                ->required(),

            ItemListFormField::create('races')
                ->label('wcf.acp.pip.rpClassification.races')
                ->description('wcf.acp.pip.rpClassification.races.description')
                ->saveValueType(ItemListFormField::SAVE_VALUE_TYPE_ARRAY)
                ->required(),

            ItemListFormField::create('roles')
                ->label('wcf.acp.pip.rpClassification.roles')
                ->description('wcf.acp.pip.rpClassification.roles.description')
                ->saveValueType(ItemListFormField::SAVE_VALUE_TYPE_ARRAY)
                ->required(),

            ItemListFormField::create('skills')
                ->label('wcf.acp.pip.rpClassification.skills')
                ->description('wcf.acp.pip.rpClassification.skills.description')
                ->saveValueType(ItemListFormField::SAVE_VALUE_TYPE_ARRAY)
                ->required(),

            TextFormField::create('icon')
                ->label('wcf.acp.pip.rpClassification.icon')
                ->description('wcf.acp.pip.rpClassification.icon.description'),
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

        $races = $element->getElementsByTagName('races')->item(0);
        if ($races !== null) {
            $entry = [];
            /** @var \DOMElement $race */
            foreach ($races->getElementsByTagName('race') as $race) {
                $entry[] = $race->nodeValue;
            }

            if (!empty($entry)) {
                $data['race'] = $entry;
            }
        }

        $roles = $element->getElementsByTagName('roles')->item(0);
        if ($roles !== null) {
            $entry = [];
            /** @var \DOMElement $role */
            foreach ($roles->getElementsByTagName('role') as $role) {
                $entry[] = $role->nodeValue;
            }

            if (!empty($entry)) {
                $data['roles'] = $entry;
            }
        }

        $skills = $element->getElementsByTagName('skills')->item(0);
        if ($skills !== null) {
            $entry = [];
            /** @var \DOMElement $skill */
            foreach ($skills->getElementsByTagName('skill') as $skill) {
                $entry[] = $skill->nodeValue;
            }

            if (!empty($entry)) {
                $data['skills'] = $entry;
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

            if (isset($data['races'])) {
                $this->races[$data['identifier']] = $data['races'];
                unset($data['races']);
            }

            if (isset($data['roles'])) {
                $this->roles[$data['identifier']] = $data['roles'];
                unset($data['roles']);
            }

            if (isset($data['skills'])) {
                $this->skills[$data['identifier']] = $data['skills'];
                unset($data['skills']);
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
                FROM    rp" . WCF_N . "_classification
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
        return 'rpClassification.xml';
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
                throw new SystemException("Missing required attribute 'language' for classification item '" . $element->parentNode->getAttribute('identifier') . "'");
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
        } else if ($element->tagName == 'races') {
            $nodeValue = [];

            $races = $xpath->query('child::ns:race', $element);
            foreach ($races as $race) {
                $nodeValue[] = $race->nodeValue;
            }

            $elements['races'] = $nodeValue;
        } else if ($element->tagName == 'roles') {
            $nodeValue = [];

            $roles = $xpath->query('child::ns:role', $element);
            foreach ($roles as $role) {
                $nodeValue[] = $role->nodeValue;
            }

            $elements['roles'] = $nodeValue;
        } else if ($element->tagName == 'skills') {
            $nodeValue = [];

            $skills = $xpath->query('child::ns:skill', $element);
            foreach ($skills as $skill) {
                $nodeValue[] = $skill->nodeValue;
            }

            $elements['skills'] = $nodeValue;
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
        return [
            'language',
            'rpGame',
            'rpFaction',
            'rpRace',
            'rpRole',
            'rpSkill'
        ];
    }

    /**
     * @inheritDoc
     */
    protected function handleDelete(array $items)
    {
        $sql = "DELETE FROM rp1_classification
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
                'rp.classification.' . $item['attributes']['identifier'],
            ]);
        }
        WCF::getDB()->commitTransaction();
    }

    /**
     * @inheritDoc
     */
    protected function import(array $row, array $data): IStorableObject
    {
        $classification = parent::import($row, $data);

        $this->classifications[$classification->classificationID] = ($classification instanceof Classification) ? new ClassificationEditor($classification) : $classification;

        return $classification;
    }

    /**
     * @inheritDoc
     */
    protected function postImport(): void
    {
        if (!empty($this->factions)) {
            $this->postImportFactions();
        }

        if (!empty($this->races)) {
            $this->postImportRaces();
        }

        if (!empty($this->roles)) {
            $this->postImportRoles();
        }

        if (!empty($this->skills)) {
            $this->postImportSkills();
        }
    }

    protected function postImportFactions(): void
    {
        $conditions = new PreparedStatementConditionBuilder();
        $conditions->add('identifier IN (?)', [\array_keys($this->factions)]);
        $conditions->add('packageID = ?', [$this->installation->getPackageID()]);

        $sql = "SELECT  *
                FROM    rp1_classification
                {$conditions}";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute($conditions->getParameters());

        /** @var Classification[] $classifications */
        $classifications = $statement->fetchObjects(Classification::class, 'identifier');

        // save factions
        $sql = "DELETE FROM rp1_classification_to_faction
                WHERE       classificationID = ?";
        $deleteStatement = WCF::getDB()->prepare($sql);

        $sql = "INSERT IGNORE   rp1_classification_to_faction
                                (classificationID, factionID)
                VALUES          (?, ?)";
        $insertStatement = WCF::getDB()->prepare($sql);

        foreach ($this->factions as $classificationIdentifier => $factions) {
            // delete old factions
            $deleteStatement->execute([$classifications[$classificationIdentifier]->classificationID]);

            // get faction ids
            $conditionBuilder = new PreparedStatementConditionBuilder();
            $conditionBuilder->add('identifier IN (?)', [$factions]);
            $conditionBuilder->add('packageID = ?', [$this->installation->getPackageID()]);
            $sql = "SELECT  factionID
                    FROM    rp1_faction
                    {$conditionBuilder}";
            $statement = WCF::getDB()->prepare($sql);
            $statement->execute($conditionBuilder->getParameters());
            $factionIDs = $statement->fetchAll(\PDO::FETCH_COLUMN);

            // save faction ids
            foreach ($factionIDs as $factionID) {
                $insertStatement->execute([
                    $classifications[$classificationIdentifier]->classificationID,
                    $factionID,
                ]);
            }
        }
    }

    protected function postImportRaces(): void
    {
        $conditions = new PreparedStatementConditionBuilder();
        $conditions->add('identifier IN (?)', [\array_keys($this->races)]);
        $conditions->add('packageID = ?', [$this->installation->getPackageID()]);

        $sql = "SELECT  *
                FROM    rp1_classification
                {$conditions}";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute($conditions->getParameters());

        /** @var Classification[] $classifications */
        $classifications = $statement->fetchObjects(Classification::class, 'identifier');

        // save races
        $sql = "DELETE FROM rp1_classification_to_race
                WHERE       classificationID = ?";
        $deleteStatement = WCF::getDB()->prepare($sql);

        $sql = "INSERT IGNORE   rp1_classification_to_race
                                (classificationID, raceID)
                VALUES          (?, ?)";
        $insertStatement = WCF::getDB()->prepare($sql);

        foreach ($this->races as $classificationIdentifier => $races) {
            // delete old races
            $deleteStatement->execute([$classifications[$classificationIdentifier]->classificationID]);

            // get race ids
            $conditionBuilder = new PreparedStatementConditionBuilder();
            $conditionBuilder->add('identifier IN (?)', [$races]);
            $conditionBuilder->add('packageID = ?', [$this->installation->getPackageID()]);
            $sql = "SELECT  raceID
                    FROM    rp1_race
                    {$conditionBuilder}";
            $statement = WCF::getDB()->prepare($sql);
            $statement->execute($conditionBuilder->getParameters());
            $raceIDs = $statement->fetchAll(\PDO::FETCH_COLUMN);

            // save race ids
            foreach ($raceIDs as $raceID) {
                $insertStatement->execute([
                    $classifications[$classificationIdentifier]->classificationID,
                    $raceID,
                ]);
            }
        }
    }

    protected function postImportRoles(): void
    {
        $conditions = new PreparedStatementConditionBuilder();
        $conditions->add('identifier IN (?)', [\array_keys($this->roles)]);
        $conditions->add('packageID = ?', [$this->installation->getPackageID()]);

        $sql = "SELECT  *
                FROM    rp1_classification
                {$conditions}";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute($conditions->getParameters());

        /** @var Classification[] $classifications */
        $classifications = $statement->fetchObjects(Classification::class, 'identifier');

        // save roles
        $sql = "DELETE FROM rp1_classification_to_role
                WHERE       classificationID = ?";
        $deleteStatement = WCF::getDB()->prepare($sql);

        $sql = "INSERT IGNORE   rp1_classification_to_role
                                (classificationID, roleID)
                VALUES          (?, ?)";
        $insertStatement = WCF::getDB()->prepare($sql);

        foreach ($this->roles as $classificationIdentifier => $roles) {
            // delete old roles
            $deleteStatement->execute([$classifications[$classificationIdentifier]->classificationID]);

            // get role ids
            $conditionBuilder = new PreparedStatementConditionBuilder();
            $conditionBuilder->add('identifier IN (?)', [$roles]);
            $conditionBuilder->add('packageID = ?', [$this->installation->getPackageID()]);
            $sql = "SELECT  roleID
                    FROM    rp1_role
                    {$conditionBuilder}";
            $statement = WCF::getDB()->prepare($sql);
            $statement->execute($conditionBuilder->getParameters());
            $roleIDs = $statement->fetchAll(\PDO::FETCH_COLUMN);

            // save role ids
            foreach ($roleIDs as $roleID) {
                $insertStatement->execute([
                    $classifications[$classificationIdentifier]->classificationID,
                    $roleID,
                ]);
            }
        }
    }

    protected function postImportSkills(): void
    {
        $conditions = new PreparedStatementConditionBuilder();
        $conditions->add('identifier IN (?)', [\array_keys($this->skills)]);
        $conditions->add('packageID = ?', [$this->installation->getPackageID()]);

        $sql = "SELECT  *
                FROM    rp1_classification
                {$conditions}";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute($conditions->getParameters());

        /** @var Classification[] $classifications */
        $classifications = $statement->fetchObjects(Classification::class, 'identifier');

        // save skills
        $sql = "DELETE FROM rp1_classification_to_skill
                WHERE       classificationID = ?";
        $deleteStatement = WCF::getDB()->prepare($sql);

        $sql = "INSERT IGNORE   rp1_classification_to_skill
                                (classificationID, skillID)
                VALUES          (?, ?)";
        $insertStatement = WCF::getDB()->prepare($sql);

        foreach ($this->skills as $classificationIdentifier => $skills) {
            // delete old skills
            $deleteStatement->execute([$classifications[$classificationIdentifier]->classificationID]);

            // get skill ids
            $conditionBuilder = new PreparedStatementConditionBuilder();
            $conditionBuilder->add('identifier IN (?)', [$skills]);
            $conditionBuilder->add('packageID = ?', [$this->installation->getPackageID()]);
            $sql = "SELECT  skillID
                    FROM    rp1_skill
                    {$conditionBuilder}";
            $statement = WCF::getDB()->prepare($sql);
            $statement->execute($conditionBuilder->getParameters());
            $skillIDs = $statement->fetchAll(\PDO::FETCH_COLUMN);

            // save skill ids
            foreach ($skillIDs as $skillID) {
                $insertStatement->execute([
                    $classifications[$classificationIdentifier]->classificationID,
                    $skillID,
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

        $classification = $document->createElement($this->tagName);
        $classification->setAttribute('identifier', $data['identifier']);

        if (!empty($data['game'])) {
            $classification->appendChild($document->createElement('game', $data['game']));
        }

        foreach ($formData['title_i18n'] as $languageID => $title) {
            $title = $document->createElement('title', $this->getAutoCdataValue($title));
            $title->setAttribute('language', LanguageFactory::getInstance()->getLanguage($languageID)->languageCode);

            $classification->appendChild($title);
        }

        $this->appendElementChildren(
            $classification,
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

            $classification->appendChild($factions);
        }

        if (!empty($formData['races'])) {
            $races = $document->createElement('races');

            \sort($formData['races']);
            foreach ($formData['races'] as $race) {
                $races->appendChild($document->createElement('race', $race));
            }

            $classification->appendChild($races);
        }

        if (!empty($formData['roles'])) {
            $roles = $document->createElement('roles');

            \sort($formData['roles']);
            foreach ($formData['roles'] as $role) {
                $roles->appendChild($document->createElement('role', $role));
            }

            $classification->appendChild($roles);
        }

        if (!empty($formData['skills'])) {
            $skills = $document->createElement('skills');

            \sort($formData['skills']);
            foreach ($formData['skills'] as $skill) {
                $skills->appendChild($document->createElement('skill', $skill));
            }

            $classification->appendChild($skills);
        }

        return $classification;
    }

    /**
     * @inheritDoc
     * @throws  SystemException
     */
    protected function prepareImport(array $data): array
    {
        $gameID = $this->getGameCacheData()->getGameByIdentifier($data['elements']['game'] ?? '')?->gameID;

        if ($gameID === null) {
            throw new SystemException("The classification '" . $data['attributes']['identifier'] . "' must either have an associated game or unable to find game '" . $data['elements']['game'] . "'.");
        }

        if (!empty($data['elements']['factions'])) {
            $this->factions[$data['attributes']['identifier']] = $data['elements']['factions'];
        }

        if (!empty($data['elements']['races'])) {
            $this->races[$data['attributes']['identifier']] = $data['elements']['races'];
        }

        if (!empty($data['elements']['roles'])) {
            $this->roles[$data['attributes']['identifier']] = $data['elements']['roles'];
        }

        if (!empty($data['elements']['skills'])) {
            $this->skills[$data['attributes']['identifier']] = $data['elements']['skills'];
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
            'identifier' => 'wcf.acp.pip.rpClassification.identifier',
            'game' => 'wcf.acp.pip.rpClassification.game',
        ]);
    }
}
