<?php

namespace rp\data\point\account;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\language\item\LanguageItemAction;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\language\I18nHandler;
use wcf\system\WCF;

/**
 * Executes point account related actions.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @method  PointAccountEditor[]    getObjects()
 * @method  PointAccountEditor  getSingleObject()
 */
class PointAccountAction extends AbstractDatabaseObjectAction
{
    protected $className = PointAccountEditor::class;
    protected $permissionsCreate = ['admin.rp.canManagePointAccount'];
    protected $permissionsDelete = ['admin.rp.canManagePointAccount'];
    protected $permissionsUpdate = ['admin.rp.canManagePointAccount'];
    protected $requireACP = ['create', 'delete', 'update'];

    #[\Override]
    public function create(): PointAccount
    {
        $this->parameters['data']['game'] ??= \RP_CURRENT_GAME;

        // The title cannot be empty by design, but cannot be filled proper if the
        // multilingualism is enabled, therefore, we must fill the title with a dummy value.
        if (!isset($this->parameters['data']['title']) && isset($this->parameters['title_i18n'])) {
            $this->parameters['data']['title'] = 'wcf.global.name';
        }

        // The description cannot be empty by design, but cannot be filled proper if the
        // multilingualism is enabled, therefore, we must fill the description with a dummy value.
        if (!isset($this->parameters['data']['description']) && isset($this->parameters['description_i18n'])) {
            $this->parameters['data']['description'] = 'wcf.global.description';
        }

        /** @var PointAccount $account */
        $account = parent::create();

        // i18n
        $updateData = [];
        if (isset($this->parameters['title_i18n'])) {
            I18nHandler::getInstance()->save(
                $this->parameters['title_i18n'],
                'rp.point.account.title' . $account->accountID,
                'rp.point.account',
                $account->game
            );

            $updateData['title'] = 'rp.point.account.title' . $account->accountID;
        }
        if (isset($this->parameters['description_i18n'])) {
            I18nHandler::getInstance()->save(
                $this->parameters['description_i18n'],
                'rp.point.account.description' . $account->accountID,
                'rp.point.account',
                $account->game
            );

            $updateData['description'] = 'rp.point.account.description' . $account->accountID;
        }

        if (!empty($updateData)) {
            $accountEditor = new PointAccountEditor($account);
            $accountEditor->update($updateData);
        }

        return $account;
    }

    #[\Override]
    public function delete(): void
    {
        throw new \BadMethodCallException(
            'Deleting point accounts is not supported via the PointAccountAction class. ' .
            'Use the DeleteAccounts command instead.'
        );
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
                    'rp.point.account.title' . $object->accountID,
                    'rp.point.account',
                    $object->game
                );

                $updateData['title'] = 'rp.point.account.title' . $object->accountID;
            }

            if (isset($this->parameters['description_i18n'])) {
                I18nHandler::getInstance()->save(
                    $this->parameters['description_i18n'],
                    'rp.point.account.description' . $object->accountID,
                    'rp.point.account',
                    $object->game
                );

                $updateData['description'] = 'rp.point.account.description' . $object->accountID;
            }

            if (!empty($updateData)) {
                $object->update($updateData);
            }
        }
    }
}
