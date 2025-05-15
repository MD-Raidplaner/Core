<?php

namespace wcf\system\package\plugin;

use rp\data\game\GameEditor;
use rp\data\game\GameList;
use wcf\system\devtools\pip\IDevtoolsPipEntryList;
use wcf\system\devtools\pip\IGuiPackageInstallationPlugin;
use wcf\system\devtools\pip\TXmlGuiPackageInstallationPlugin;
use wcf\system\exception\SystemException;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\TitleFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\form\builder\IFormDocument;
use wcf\system\language\LanguageFactory;
use wcf\system\Regex;
use wcf\system\WCF;

/**
 * Installs, updates and deletes games.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class RPGamePackageInstallationPlugin extends AbstractXMLPackageInstallationPlugin implements IGuiPackageInstallationPlugin, IUniqueNameXMLPackageInstallationPlugin
{
    use TXmlGuiPackageInstallationPlugin;

    /**
     * @inheritDoc
     */
    public $application = 'rp';

    /**
     * @inheritDoc
     */
    public $className = GameEditor::class;

    /**
     * @inheritDoc
     */
    public $tableName = 'game';

    /**
     * @inheritDoc
     */
    public $tagName = 'game';

    /**
     * @inheritDoc
     */
    protected function addFormFields(IFormDocument $form): void
    {
        /** @var FormContainer $dataContainer */
        $dataContainer = $form->getNodeById('data');

        $dataContainer->appendChildren([
            TextFormField::create('identifier')
                ->label('wcf.acp.pip.rpGame.identifier')
                ->description('wcf.acp.pip.rpGame.identifier.description')
                ->required()
                ->addValidator(new FormFieldValidator('regex', function (TextFormField $formField) {
                    $regex = Regex::compile('^[A-z0-9\-\_]+$');

                    if (!$regex->match($formField->getSaveValue())) {
                        $formField->addValidationError(
                            new FormFieldValidationError(
                                'invalid',
                                'wcf.acp.pip.rpGame.identifier.error.invalid'
                            )
                        );
                    }
                }))
                ->addValidator(new FormFieldValidator('uniqueness', function (TextFormField $formField) {
                    if (
                        $formField->getDocument()->getFormMode() === IFormDocument::FORM_MODE_CREATE
                        || $this->editedEntry->getAttribute('identifier') !== $formField->getValue()
                    ) {
                        $gameList = new GameList();
                        $gameList->getConditionBuilder()->add('identifier = ?', [$formField->getValue()]);

                        if ($gameList->countObjects() > 0) {
                            $formField->addValidationError(
                                new FormFieldValidationError(
                                    'notUnique',
                                    'wcf.acp.pip.rpGame.identifier.error.noUnique'
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

        if ($saveData) {
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
    protected function findExistingItem(array $data): ?array
    {
        $sql = "SELECT  *
                FROM    rp" . WCF_N . "_game
                WHERE   identifier = ?
                    AND packageID = ?";

        $parameters = [
            $data['identifier'],
            $this->installation->getPackageID(),
        ];

        return [
            'parameters' => $parameters,
            'sql' => $sql,
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getDefaultFilename(): string
    {
        return 'rpGame.xml';
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
                throw new SystemException("Missing required attribute 'language' for game '" . $element->parentNode->getAttribute('identifier') . "'.");
            }

            // <title> can occur multiple times using the `language` attribute
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
    public static function getSyncDependencies(): array
    {
        return ['language'];
    }

    /**
     * @inheritDoc
     */
    protected function handleDelete(array $items): void
    {
        $sql = "DELETE FROM rp1_game
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
                'rp.game.' . $item['attributes']['identifier'],
            ]);
        }
        WCF::getDB()->commitTransaction();
    }

    protected function prepareXmlElement(\DOMDocument $document, IFormDocument $form): \DOMElement
    {
        $formData = $form->getData();

        $game = $document->createElement($this->tagName);
        $game->setAttribute('identifier', $formData['data']['identifier']);

        foreach ($formData['title_i18n'] as $languageID => $title) {
            $title = $document->createElement('title', $this->getAutoCdataValue($title));
            $title->setAttribute('language', LanguageFactory::getInstance()->getLanguage($languageID)->languageCode);

            $game->appendChild($title);
        }

        return $game;
    }

    /**
     * @inheritDoc
     */
    protected function prepareImport(array $data): array
    {
        return [
            'identifier' => $data['attributes']['identifier'],
            'title' => $this->getI18nValues($data['elements']['title']),
        ];
    }

    protected function setEntryListKeys(IDevtoolsPipEntryList $entryList): void
    {
        $entryList->setKeys([
            'identifier' => 'wcf.acp.pip.rpGame.identifier',
        ]);
    }
}
