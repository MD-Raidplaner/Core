<?php

/**
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */

use wcf\system\event\EventHandler;

return new class {
    public function __invoke(): void
    {
        $this->initEndpoints();
        $this->initGame();
        $this->initACPMenuItems();
    }

    private function initACPMenuItems(): void
    {
        EventHandler::getInstance()->register(
            \wcf\event\acp\menu\item\ItemCollecting::class,
            \rp\system\event\listener\AcpMenuItemCollectingListener::class
        );
    }

    private function initEndpoints(): void
    {
        EventHandler::getInstance()->register(
            \wcf\event\endpoint\ControllerCollecting::class,
            static function (\wcf\event\endpoint\ControllerCollecting $event) {
                $event->register(new \wcf\system\endpoint\controller\rp\core\characters\DeleteCharacter);
                $event->register(new \wcf\system\endpoint\controller\rp\core\characters\DisableCharacter);
                $event->register(new \wcf\system\endpoint\controller\rp\core\characters\EnableCharacter);
                $event->register(new \wcf\system\endpoint\controller\rp\core\characters\SetPrimaryCharacter);
            }
        );
    }

    private function initGame(): void
    {
        EventHandler::getInstance()->register(
            \rp\event\game\GameCollecting::class,
            static function (\rp\event\game\GameCollecting $event): void {
                $event->register(new \rp\system\game\Game('default'));
            }
        );
    }
};
