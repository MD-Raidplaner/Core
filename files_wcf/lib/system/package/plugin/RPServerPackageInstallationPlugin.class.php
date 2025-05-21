<?php

namespace wcf\system\package\plugin;

use rp\data\server\ServerEditor;
use rp\data\server\ServerList;
use rp\system\cache\eager\data\GameCacheData;
use rp\system\cache\eager\GameCache;
use wcf\system\devtools\pip\IDevtoolsPipEntryList;
use wcf\system\devtools\pip\IGuiPackageInstallationPlugin;
use wcf\system\devtools\pip\TXmlGuiPackageInstallationPlugin;
use wcf\system\exception\SystemException;
use wcf\system\form\builder\container\FormContainer;
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
 * Installs, updates and deletes servers.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class RPServerPackageInstallationPlugin extends AbstractXMLPackageInstallationPlugin implements
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
    public $className = ServerEditor::class;

    /**
     * @inheritDoc
     */
    public $tableName = 'server';

    /**
     * @inheritDoc
     */
    public $tagName = 'server';

    /**
     * @inheritDoc
     */
    protected function addFormFields(IFormDocument $form): void
    {
        /** @var FormContainer $dataContainer */
        $dataContainer = $form->getNodeById('data');

        $dataContainer->appendChildren([
            TextFormField::create('identifier')
                ->label('wcf.acp.pip.rpServer.identifier')
                ->description('wcf.acp.pip.rpServer.identifier.description')
                ->required()
                ->addValidator(new FormFieldValidator('regex', function (TextFormField $formField) {
                    $regex = Regex::compile('^[A-z0-9\-\_]+$');

                    if (!$regex->match($formField->getSaveValue())) {
                        $formField->addValidationError(
                            new FormFieldValidationError(
                                'invalid',
                                'wcf.acp.pip.rpServer.identifier.error.invalid'
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

                        $serverList = new ServerList();
                        $serverList->getConditionBuilder()->add('identifier = ?', [$formField->getValue()]);

                        if ($serverList->countObjects() > 0) {
                            $formField->addValidationError(
                                new FormFieldValidationError(
                                    'notUnique',
                                    'wcf.acp.pip.rpServer.identifier.error.noUnique'
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
                ->label('wcf.acp.pip.rpServer.game')
                ->description('wcf.acp.pip.rpServer.game.description')
                ->required()
                ->options(function () {
                    $options = [];
                    foreach ($this->getGameCacheData()->games as $game) {
                        $options[$game->identifier] = $game->getTitle();
                    }

                    \asort($options);

                    return $options;
                }),

            TextFormField::create('serverGroup')
                ->label('wcf.acp.pip.rpServer.serverGroup')
                ->description('wcf.acp.pip.rpServer.serverGroup.description'),

            TextFormField::create('type')
                ->label('wcf.acp.pip.rpServer.type')
                ->description('wcf.acp.pip.rpServer.type.description'),
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

        foreach (['game', 'serverGroup', 'type'] as $optionalElementName) {
            $optionalElement = $element->getElementsByTagName($optionalElementName)->item(0);
            if ($optionalElement !== null) {
                $data[$optionalElementName] = $optionalElement->nodeValue;
            } elseif ($saveData) {
                $data[$optionalElementName] = '';
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
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    protected function findExistingItem(array $data): array
    {
        $sql = "SELECT  *
                FROM    rp" . WCF_N . "_server
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
        return 'rpServer.xml';
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
                throw new SystemException("Missing required attribute 'language' for server item '" . $element->parentNode->getAttribute('identifier') . "'");
            }

            // <title> can occur multiple items using the `language` attribute
            $elements['title'] ??= [];

            $elements['title'][$element->getAttribute('language')] = $element->nodeValue;
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
        return ['language', 'rpGame'];
    }

    /**
     * @inheritDoc
     */
    protected function handleDelete(array $items)
    {
        $sql = "DELETE FROM rp1_server
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
                'rp.server.' . $item['attributes']['identifier'],
            ]);
        }
        WCF::getDB()->commitTransaction();
    }

    /**
     * @inheritDoc
     */
    protected function prepareXmlElement(\DOMDocument $document, IFormDocument $form): \DOMElement
    {
        $formData = $form->getData();
        $data = $formData['data'];

        $server = $document->createElement($this->tagName);
        $server->setAttribute('identifier', $data['identifier']);

        if (!empty($data['game'])) {
            $server->appendChild($document->createElement('game', $data['game']));
        }

        foreach ($formData['title_i18n'] as $languageID => $title) {
            $title = $document->createElement('title', $this->getAutoCdataValue($title));
            $title->setAttribute('language', LanguageFactory::getInstance()->getLanguage($languageID)->languageCode);

            $server->appendChild($title);
        }

        $this->appendElementChildren(
            $server,
            [
                'serverGroup' => '',
                'type' => '',
            ],
            $form
        );

        return $server;
    }

    /**
     * @inheritDoc
     * @throws  SystemException
     */
    protected function prepareImport(array $data): array
    {
        $gameID = $this->getGameCacheData()->getGameByIdentifier($data['elements']['game'] ?? '')?->gameID;

        if ($gameID === null) {
            throw new SystemException("The server '" . $data['attributes']['identifier'] . "' must either have an associated game or unable to find game '" . $data['elements']['game'] . "'.");
        }

        return [
            'gameID' => $gameID,
            'identifier' => $data['attributes']['identifier'],
            'serverGroup' => $data['elements']['serverGroup'] ?? '',
            'title' => $this->getI18nValues($data['elements']['title']),
            'type' => $data['elements']['type'] ?? '',
        ];
    }

    /**
     * @inheritDoc
     */
    protected function setEntryListKeys(IDevtoolsPipEntryList $entryList): void
    {
        $entryList->setKeys([
            'identifier' => 'wcf.acp.pip.rpServer.identifier',
            'game' => 'wcf.acp.pip.rpServer.game',
        ]);
    }
}
