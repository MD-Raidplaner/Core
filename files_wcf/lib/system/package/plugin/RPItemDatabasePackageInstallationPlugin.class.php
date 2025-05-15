<?php

namespace wcf\system\package\plugin;

use rp\data\item\database\ItemDatabaseEditor;
use rp\data\item\database\ItemDatabaseList;
use rp\system\item\database\IItemDatabase;
use wcf\system\devtools\pip\IDevtoolsPipEntryList;
use wcf\system\devtools\pip\IGuiPackageInstallationPlugin;
use wcf\system\devtools\pip\TXmlGuiPackageInstallationPlugin;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\field\ClassNameFormField;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\form\builder\IFormDocument;
use wcf\system\WCF;

/**
 * Installs, updates and deletes item tool tip database.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class RPItemDatabasePackageInstallationPlugin extends AbstractXMLPackageInstallationPlugin implements IGuiPackageInstallationPlugin
{
    use TXmlGuiPackageInstallationPlugin;
    /**
     * @inheritDoc
     */
    public $application = 'rp';

    /**
     * @inheritDoc
     */
    public $className = ItemDatabaseEditor::class;

    /**
     * @inheritDoc
     */
    public $tableName = 'item_database';

    /**
     * @inheritDoc
     */
    public $tagName = 'database';

    /**
     * @inheritDoc
     * @since   5.2
     */
    protected function addFormFields(IFormDocument $form): void
    {
        /** @var FormContainer $dataContainer */
        $dataContainer = $form->getNodeById('data');

        $dataContainer->appendChildren([
                TextFormField::create('identifier')
                ->objectProperty('identifier')
                ->label('rp.acp.item.identifier')
                ->description('rp.acp.item.identifier.description')
                ->required()
                ->addValidator(new FormFieldValidator('format', static function (TextFormField $formField) {
                            if (\preg_match('~^[a-z][A-z]+$~', $formField->getValue()) !== 1) {
                                $formField->addValidationError(
                                    new FormFieldValidationError(
                                        'format',
                                        'rp.acp.item.identifier.error.format'
                                    )
                                );
                            }
                        }))
                ->addValidator(new FormFieldValidator('uniqueness', function (TextFormField $formField) {
                            if (
                                $formField->getDocument()->getFormMode() === IFormDocument::FORM_MODE_CREATE || $this->editedEntry->getAttribute('identifier') !== $formField->getValue()
                            ) {
                                $databaseList = new ItemDatabaseList();
                                $databaseList->getConditionBuilder()->add('identifier = ?', [$formField->getValue()]);

                                if ($databaseList->countObjects()) {
                                    $formField->addValidationError(
                                        new FormFieldValidationError(
                                            'format',
                                            'rp.acp.item.identifier.error.notUnique'
                                        )
                                    );
                                }
                            }
                        })),
                ClassNameFormField::create()
                ->required()
                ->implementedInterface(IItemDatabase::class),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function fetchElementData(\DOMElement $element, $saveData): array
    {
        return [
            'className' => $element->nodeValue,
            'identifier' => $element->getAttribute('identifier')
        ];
    }

    /**
     * @inheritDoc
     */
    protected function findExistingItem(array $data): array
    {
        $sql = "SELECT  *
                FROM    rp" . WCF_N . "_" . $this->tableName . "
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
     * @see IPackageInstallationPlugin::getDefaultFilename()
     */
    public static function getDefaultFilename(): string
    {
        return 'rpItemDatabase.xml';
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
    public static function getSyncDependencies(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    protected function handleDelete(array $items): void
    {
        $sql = "DELETE FROM rp1_" . $this->tableName . "
                WHERE       identifier = ?
                        AND packageID = ?";
        $statement = WCF::getDB()->prepare($sql);
        foreach ($items as $item) {
            $statement->execute([
                $item['attributes']['identifier'],
                $this->installation->getPackageID(),
            ]);
        }
    }

    /**
     * @inheritDoc
     */
    protected function prepareImport(array $data): array
    {
        return [
            'className' => $data['nodeValue'],
            'identifier' => $data['attributes']['identifier']
        ];
    }

    /**
     * @inheritDoc
     */
    protected function prepareXmlElement(\DOMDocument $document, IFormDocument $form)
    {
        $data = $form->getData()['data'];

        $database = $document->createElement($this->tagName, $data['className']);
        $database->setAttribute('identifier', $data['identifier']);

        return $database;
    }

    /**
     * @inheritDoc
     */
    protected function setEntryListKeys(IDevtoolsPipEntryList $entryList): void
    {
        $entryList->setKeys([
            'className' => 'wcf.form.field.className',
            'identifier' => 'rp.acp.item.identifier'
        ]);
    }
}